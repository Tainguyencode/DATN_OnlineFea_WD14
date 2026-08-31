<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InstructorFinanceService
{
    /** Lock the same instructor rows as withdrawals, before locking refunds/orders. Call inside a transaction. */
    public function lockOrderInstructors(Order $order): void
    {
        $ids = DB::table('order_items')
            ->join('courses', 'courses.id', '=', 'order_items.course_id')
            ->where('order_items.order_id', $order->id)
            ->distinct()->pluck('courses.instructor_id');

        User::query()->whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get();
    }
}
