<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Order;
use App\Models\Wishlist;
use App\Notifications\CertificateIssuedNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MiscController extends Controller
{
    public function wishlist(): View
    {
        $items = Wishlist::where('user_id', auth()->id())
            ->whereHas('course', fn ($query) => $query
                ->where('status', Course::STATUS_PUBLISHED)
                ->where('is_published', true))
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
            foreach ($certificates as $cert) {
                auth()->user()->notify(new CertificateIssuedNotification($cert->course, $cert));
            }

            return redirect()->route('student.certificates')->with('success', 'Email chứng chỉ đã được gửi tới hòm thư của bạn!');
        }

        return view('student.certificates', compact('certificates'));
    }

    public function orders(): View
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['items.course:id,title', 'payment'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('student.orders', compact('orders'));
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

    public function viewCertificatePdf(Certificate $certificate)
    {
        abort_unless((int) $certificate->user_id === (int) auth()->id(), 403);

        $certificate->load(['course', 'user']);

        $pdf = Pdf::loadView('pdf.certificate', [
            'certificate' => $certificate,
            'course' => $certificate->course,
            'user' => $certificate->user,
        ]);

        return $pdf->stream('certificate-'.$certificate->certificate_code.'.pdf');
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

    public function publicCertificatePdf(string $code)
    {
        $certificate = Certificate::where('certificate_code', $code)
            ->with(['course', 'user'])
            ->firstOrFail();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.certificate', [
            'certificate' => $certificate,
            'course' => $certificate->course,
            'user' => $certificate->user,
        ]);

        return $pdf->stream('certificate-' . $certificate->certificate_code . '.pdf');
    }
}
