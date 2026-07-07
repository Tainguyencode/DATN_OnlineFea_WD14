<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PushNotification;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $notifications = $user->pushNotifications()
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => app(NotificationService::class)->unreadCount($user),
        ]);
    }

    public function markAsRead(Request $request, PushNotification $notification, NotificationService $notificationService): RedirectResponse
    {
        $notificationService->markAsRead($notification, $request->user());

        if ($notification->url) {
            return redirect($notification->url);
        }

        return back()->with('success', 'Đã đánh dấu thông báo là đã đọc.');
    }

    public function markAllAsRead(Request $request, NotificationService $notificationService): RedirectResponse
    {
        $count = $notificationService->markAllAsRead($request->user());

        return back()->with('success', "Đã đánh dấu {$count} thông báo là đã đọc.");
    }
}
