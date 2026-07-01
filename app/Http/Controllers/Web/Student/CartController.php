<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Course;
use App\Models\Coupon;
use App\Models\Enrollment;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CartController extends Controller
{
    protected function getCart(): Cart
    {
        return Cart::firstOrCreate(['user_id' => auth()->id()]);
    }

    public function index(): View
    {
        $cart = $this->getCart()->load(['items.course.instructor:id,name']);
        $total = $cart->items->sum(fn ($i) => $i->course->discount_price ?? $i->course->sale_price ?? $i->course->price);

        return view('student.cart.index', compact('cart', 'total'));
    }

    public function add(Course $course): RedirectResponse
    {
        if ($course->status !== 'published') {
            return back()->with('error', 'Khóa học chưa được xuất bản.');
        }

        if (Enrollment::where('user_id', auth()->id())->where('course_id', $course->id)->exists()) {
            return back()->with('error', 'Bạn đã đăng ký khóa học này.');
        }

        $cart = $this->getCart();
        CartItem::firstOrCreate(['cart_id' => $cart->id, 'course_id' => $course->id]);

        return back()->with('success', 'Đã thêm vào giỏ hàng!');
    }

    public function remove(int $courseId): RedirectResponse
    {
        $cart = $this->getCart();
        CartItem::where('cart_id', $cart->id)->where('course_id', $courseId)->delete();

        return back()->with('success', 'Đã xóa khỏi giỏ hàng.');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:momo,vnpay,bank_transfer',
            'coupon_code' => 'nullable|string',
        ]);

        $cart = $this->getCart()->load('items.course');
        if ($cart->items->isEmpty()) {
            return back()->with('error', 'Giỏ hàng trống.');
        }

        $subtotal = $cart->items->sum(fn ($i) => $i->course->discount_price ?? $i->course->sale_price ?? $i->course->price);
        $discount = 0;
        $coupon = null;

        if (! empty($validated['coupon_code'])) {
            $coupon = Coupon::where('code', $validated['coupon_code'])->first();
            if ($coupon && $coupon->isValid() && $subtotal >= $coupon->min_order_amount) {
                $discount = $coupon->type === 'percent'
                    ? $subtotal * ($coupon->value / 100)
                    : min($coupon->value, $subtotal);
            }
        }

        $total = max(0, $subtotal - $discount);

        DB::transaction(function () use ($cart, $subtotal, $discount, $total, $coupon, $validated) {
            $items = $cart->items->map(function ($item) {
                return [
                    'course_id' => $item->course_id,
                    'price' => $item->course->discount_price ?? $item->course->sale_price ?? $item->course->price,
                    'title' => $item->course->title,
                ];
            })->toArray();

            $order = Order::create([
                'order_code' => 'ORD-'.strtoupper(Str::random(8)),
                'user_id' => auth()->id(),
                'coupon_id' => $coupon?->id,
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'total_amount' => $total,
                'status' => 'paid',
                'payment_method' => $validated['payment_method'],
                'transaction_id' => 'TXN-'.strtoupper(Str::random(10)),
                'items' => $items,
            ]);

            foreach ($cart->items as $item) {
                Enrollment::firstOrCreate(
                    ['user_id' => auth()->id(), 'course_id' => $item->course_id],
                    ['order_id' => $order->id]
                );

                $item->course->increment('enrollment_count');
            }

            if ($coupon) {
                $coupon->increment('used_count');
            }

            $cart->items()->delete();
        });

        return redirect()->route('student.courses')->with('success', 'Thanh toán thành công! Bạn có thể bắt đầu học ngay.');
    }
}
