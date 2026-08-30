<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Refund;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\PayoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RefundController extends Controller
{
    /**
     * Tiếp nhận Yêu cầu Hoàn tiền từ Học viên cho Đơn hàng đã thanh toán.
     */
    public function store(Request $request, Order $order, PayoutService $payoutService)
    {
        // Kiểm tra quyền sở hữu đơn hàng
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        // Kiểm tra đơn hàng phải ở trạng thái paid
        if ($order->status !== 'paid') {
            return back()->with('error', 'Chỉ có thể yêu cầu hoàn tiền cho đơn hàng đã thanh toán thành công.');
        }
        if ((float) $order->total_amount <= 0) {
            return back()->with('error', 'Đơn hàng không phát sinh thanh toán nên không có số tiền để hoàn.');
        }

        // Kiểm tra nếu đã có yêu cầu refund đang chờ hoặc đã duyệt
        $existingRefund = $order->refunds()
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingRefund) {
            return back()->with('error', 'Đơn hàng này đã có yêu cầu hoàn tiền đang xử lý hoặc đã hoàn tiền.');
        }

        // 1. Kiểm tra điều kiện thời hạn hoàn tiền (Trong vòng 7 ngày)
        $paidDate = $order->payment?->paid_at ?? $order->created_at;
        if (! $paidDate || now()->greaterThan($paidDate->copy()->addDays(7))) {
            return back()->with('error', 'Đơn hàng này đã quá thời hạn 7 ngày quy định cho phép yêu cầu hoàn tiền.');
        }

        // 2. Kiểm tra tiến độ học tập của khóa học (Tối ưu 1 Query duy nhất thay vì vòng lặp N+1)
        $courseIds = $order->items()->pluck('course_id');
        $overProgressEnrollment = Enrollment::where('user_id', auth()->id())
            ->whereIn('course_id', $courseIds)
            ->where('progress_percent', '>=', 50)
            ->with('course:id,title')
            ->first();

        if ($overProgressEnrollment) {
            $courseTitle = $overProgressEnrollment->course?->title ?? 'khóa học';

            return back()->with('error', "Không thể yêu cầu hoàn tiền do bạn đã học từ 50% tiến độ của \"{$courseTitle}\".");
        }

        // 3. Validate chi tiết định dạng dữ liệu đầu vào
        $cleanAccNum = preg_replace('/\s+/', '', $request->input('bank_account_number', ''));
        $request->merge(['bank_account_number' => $cleanAccNum]);

        $supportedBankCodes = collect($payoutService->getVietNamBanks())
            ->pluck('code')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
            'bank_code' => ['required', 'string', Rule::in($supportedBankCodes)],
            'bank_account_number' => ['required', 'string', 'regex:/^[0-9]{6,20}$/'],
            'bank_account_name' => ['required', 'string', 'min:3', 'max:100', 'regex:/^[a-zA-Z\s\p{L}]+$/u'],
        ], [
            'reason.required' => 'Vui lòng cung cấp lý do chi tiết yêu cầu hoàn tiền.',
            'reason.min' => 'Lý do hoàn tiền phải có ít nhất 10 ký tự.',
            'reason.max' => 'Lý do hoàn tiền không được vượt quá 1000 ký tự.',
            'bank_code.required' => 'Vui lòng chọn ngân hàng nhận tiền hợp lệ.',
            'bank_code.in' => 'Ngân hàng được chọn không nằm trong hệ thống hỗ trợ.',
            'bank_account_number.required' => 'Vui lòng nhập số tài khoản ngân hàng.',
            'bank_account_number.regex' => 'Số tài khoản phải gồm từ 6 đến 20 chữ số.',
            'bank_account_name.required' => 'Vui lòng nhập tên chủ tài khoản ngân hàng.',
            'bank_account_name.min' => 'Tên chủ tài khoản phải có ít nhất 3 ký tự.',
            'bank_account_name.regex' => 'Tên chủ tài khoản không chứa chữ số hoặc ký tự đặc biệt.',
        ]);

        $refund = DB::transaction(function () use ($order, $validated) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            if ($lockedOrder->status !== 'paid' || $lockedOrder->refunds()->whereIn('status', ['pending', 'processing', 'approved'])->exists()) {
                return null;
            }

            return Refund::updateOrCreate(
                ['order_id' => $lockedOrder->id],
                [
                    'user_id' => auth()->id(),
                    'amount' => $lockedOrder->total_amount,
                    'reason' => trim($validated['reason']),
                    'bank_code' => strtoupper($validated['bank_code']),
                    'bank_account_number' => $validated['bank_account_number'],
                    'bank_account_name' => mb_strtoupper(Str::ascii(preg_replace('/\s+/u', ' ', trim($validated['bank_account_name'])))),
                    'status' => 'pending',
                    'refund_method' => 'manual',
                    'transaction_reference' => null,
                    'admin_note' => null,
                    'processed_at' => null,
                ]);
        });

        if (! $refund) {
            return back()->with('error', 'Đơn hàng đã có yêu cầu hoàn tiền đang xử lý hoặc không còn hợp lệ.');
        }

        // 4. Bắn thông báo cho các Admin (Tối ưu hóa query)
        $notificationService = app(NotificationService::class);
        $studentName = auth()->user()->name ?? 'Học viên';
        $redirectUrl = route('admin.refunds.show', $refund);

        User::where('role', 'admin')->chunk(50, function ($admins) use ($notificationService, $studentName, $order, $redirectUrl) {
            foreach ($admins as $admin) {
                $notificationService->send(
                    $admin,
                    'Yêu cầu hoàn tiền mới',
                    "Học viên {$studentName} vừa gửi yêu cầu hoàn tiền cho đơn hàng #{$order->order_code}.",
                    'new_refund_request',
                    $redirectUrl
                );
            }
        });

        return back()->with('success', 'Yêu cầu hoàn tiền của bạn đã được gửi thành công. Ban quản trị sẽ đối soát và xử lý sớm nhất.');
    }
}
