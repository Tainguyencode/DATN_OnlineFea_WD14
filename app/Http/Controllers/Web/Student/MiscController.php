<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Order;
use App\Models\Enrollment;
use App\Services\PayoutService;
use App\Models\Wishlist;
use App\Notifications\CertificateIssuedNotification;
use App\Services\CertificatePdfService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class MiscController extends Controller
{
    public function wishlist(): View
    {
        $items = Wishlist::where('user_id', auth()->id())
            ->whereHas('course', fn ($query) => $query->published())
            ->with(['course' => fn ($query) => $query
                ->with(['instructor:id,name,avatar', 'category:id,parent_id,name,slug', 'category.parent:id,name,slug'])
                ->withCount(['lessons', 'courseSections'])])
            ->orderByDesc('created_at')
            ->paginate(9);

        return view('student.wishlist', compact('items'));
    }

    public function storeFavorite(Request $request, Course $course): JsonResponse|RedirectResponse
    {
        if (! $course->isPublished()) {
            return $this->favoriteResponse(
                $request,
                false,
                'Chỉ có thể yêu thích khóa học đang được xuất bản.',
                404
            );
        }

        $wishlist = Wishlist::firstOrCreate([
            'user_id' => $request->user()->id,
            'course_id' => $course->id,
        ]);

        return $this->favoriteResponse(
            $request,
            true,
            $wishlist->wasRecentlyCreated
                ? 'Đã thêm khóa học vào danh sách yêu thích.'
                : 'Khóa học đã có trong danh sách yêu thích.'
        );
    }

    public function destroyFavorite(Request $request, Course $course): JsonResponse|RedirectResponse
    {
        $deleted = Wishlist::where('user_id', $request->user()->id)
            ->where('course_id', $course->id)
            ->delete();

        if (! $deleted) {
            return $this->favoriteResponse(
                $request,
                false,
                'Khóa học không nằm trong danh sách yêu thích của bạn.',
                404
            );
        }

        return $this->favoriteResponse($request, false, 'Đã bỏ khóa học khỏi danh sách yêu thích.');
    }

    public function toggleWishlist(Request $request, int $courseId): JsonResponse|RedirectResponse
    {
        $course = Course::findOrFail($courseId);
        $existing = Wishlist::where('user_id', $request->user()->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return $this->favoriteResponse($request, false, 'Đã bỏ khóa học khỏi danh sách yêu thích.');
        }

        return $this->storeFavorite($request, $course);
    }

    public function certificates(Request $request)
    {
        $certificates = Certificate::where('user_id', auth()->id())
            ->with('course:id,title,thumbnail')
            ->orderByDesc('issued_at')
            ->get();

        if ($request->has('send_email')) {
            $toSend = $certificates;
            if ($request->filled('certificate_id')) {
                $toSend = $certificates->where('id', (int) $request->integer('certificate_id'))->values();
            }

            $sent = 0;
            foreach ($toSend as $cert) {
                try {
                    app(CertificatePdfService::class)->ensureStored($cert);
                    auth()->user()->notify(new CertificateIssuedNotification($cert->course, $cert->fresh()));
                    $sent++;
                } catch (Throwable $exception) {
                    Log::warning('Resend certificate email failed.', [
                        'certificate_id' => $cert->id,
                        'exception' => $exception::class,
                        'message' => $exception->getMessage(),
                    ]);
                }
            }

            if ($sent > 0) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Email chứng chỉ đã được gửi tới hòm thư của bạn!',
                    ]);
                }

                return redirect()->route('student.certificates')
                    ->with('success', 'Email chứng chỉ đã được gửi tới hòm thư của bạn!');
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không gửi được email chứng chỉ. Vui lòng thử lại sau.',
                ], 422);
            }

            return redirect()->route('student.certificates')
                ->with('error', 'Không gửi được email chứng chỉ. Vui lòng thử lại sau.');
        }

        return view('student.certificates', compact('certificates'));
    }

    public function orders(Request $request): View
    {
        $status = $request->query('status');

        $query = Order::where('user_id', auth()->id())
            ->with(['items.course' => fn ($q) => $q->with('instructor:id,name,avatar'), 'payment', 'coupon']);

        if ($status && in_array($status, ['paid', 'pending', 'cancelled', 'failed', 'refunded'], true)) {
            $query->where('status', $status);
        }

        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                    ->orWhereHas('items.course', function ($cq) use ($search) {
                        $cq->where('title', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

        return view('student.orders', compact('orders', 'status'));
    }

    public function showOrder(Order $order): View
    {
        abort_unless((int) $order->user_id === (int) auth()->id(), 403, 'Bạn không có quyền xem đơn hàng này.');

        $order->load([
            'items.course' => fn ($query) => $query->with('instructor:id,name,avatar')->withCount('lessons'),
            'payment',
            'coupon',
            'refunds' => fn ($query) => $query->latest(),
        ]);

        $paidAt = $order->payment?->paid_at ?? $order->created_at;
        $refundDeadline = $paidAt?->copy()->addDays(7);
        $maxProgress = (float) Enrollment::query()
            ->where('user_id', auth()->id())
            ->whereIn('course_id', $order->items->pluck('course_id'))
            ->max('progress_percent');
        $refundEligibility = [
            'within_window' => $refundDeadline?->isFuture() ?? false,
            'deadline' => $refundDeadline,
            'progress_ok' => $maxProgress < 50,
            'max_progress' => $maxProgress,
            'has_value' => (float) $order->total_amount > 0,
        ];
        $banks = app(PayoutService::class)->getVietNamBanks();

        return view('student.orders.show', compact('order', 'banks', 'refundEligibility'));
    }

    public function profile(): View
    {
        return view('student.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
        ]);

        auth()->user()->update($validated);

        return back()->with('success', 'Cập nhật hồ sơ thành công!');
    }

    private function favoriteResponse(
        Request $request,
        bool $favorited,
        string $message,
        int $status = 200
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => $status < 400,
                'favorited' => $favorited,
                'message' => $message,
            ], $status);
        }

        return back()->with($status < 400 ? 'success' : 'error', $message);
    }

    public function viewCertificatePdf(Request $request, Certificate $certificate)
    {
        abort_unless((int) $certificate->user_id === (int) auth()->id(), 403);

        $certificate->load(['course', 'user']);
        $pdfService = app(CertificatePdfService::class);
        $certificate = $pdfService->ensureStored($certificate);

        $absolutePath = $pdfService->absolutePath($certificate);
        $fileName = 'certificate-'.$certificate->certificate_code.'.pdf';

        if ($absolutePath) {
            $disposition = $request->boolean('download') ? 'attachment' : 'inline';

            return response()->file($absolutePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => $disposition.'; filename="'.$fileName.'"',
            ]);
        }

        $pdf = Pdf::loadView('pdf.certificate', [
            'certificate' => $certificate,
            'course' => $certificate->course,
            'user' => $certificate->user,
        ]);

        return $request->boolean('download')
            ? $pdf->download($fileName)
            : $pdf->stream($fileName);
    }

    public function publicCertificate(string $code)
    {
        $certificate = Certificate::where('certificate_code', $code)
            ->with(['course', 'user'])
            ->firstOrFail();

        return view('pdf.certificate', [
            'certificate' => $certificate,
            'course' => $certificate->course,
            'user' => $certificate->user,
            'isPublic' => true,
        ]);
    }

    public function publicCertificatePdf(Request $request, string $code)
    {
        $certificate = Certificate::where('certificate_code', $code)
            ->with(['course', 'user'])
            ->firstOrFail();

        $pdfService = app(CertificatePdfService::class);
        $certificate = $pdfService->ensureStored($certificate);

        $absolutePath = $pdfService->absolutePath($certificate);
        $fileName = 'certificate-'.$certificate->certificate_code.'.pdf';

        if ($absolutePath) {
            $disposition = $request->boolean('download') ? 'attachment' : 'inline';

            return response()->file($absolutePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => $disposition.'; filename="'.$fileName.'"',
            ]);
        }

        $pdf = Pdf::loadView('pdf.certificate', [
            'certificate' => $certificate,
            'course' => $certificate->course,
            'user' => $certificate->user,
        ]);

        return $request->boolean('download')
            ? $pdf->download($fileName)
            : $pdf->stream($fileName);
    }
}
