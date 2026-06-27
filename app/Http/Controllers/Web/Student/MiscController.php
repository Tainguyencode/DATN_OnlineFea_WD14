<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Order;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MiscController extends Controller
{
    public function wishlist(): View
    {
        $items = Wishlist::where('user_id', auth()->id())
            ->with(['course.instructor:id,name', 'course.category:id,name'])
            ->orderByDesc('created_at')
            ->get();

        return view('student.wishlist', compact('items'));
    }

    public function toggleWishlist(int $courseId): RedirectResponse
    {
        $existing = Wishlist::where('user_id', auth()->id())->where('course_id', $courseId)->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', 'Đã xóa khỏi yêu thích.');
        }

        Wishlist::create(['user_id' => auth()->id(), 'course_id' => $courseId]);
        return back()->with('success', 'Đã thêm vào yêu thích!');
    }

    public function certificates(): View
    {
        $certificates = Certificate::where('user_id', auth()->id())
            ->with('course:id,title,thumbnail')
            ->orderByDesc('issued_at')
            ->get();

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
}
