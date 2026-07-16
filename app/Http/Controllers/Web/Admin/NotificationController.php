<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SendNotificationRequest;
use App\Models\Course;
use App\Models\PushNotification;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $recentBroadcasts = PushNotification::query()
            ->where('type', 'announcement')
            ->select('title', 'message', 'url', 'created_at')
            ->selectRaw('COUNT(*) as recipient_count')
            ->groupBy('title', 'message', 'url', 'created_at')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.notifications.index', [
            'courses' => Course::query()->orderBy('title')->get(['id', 'title']),
            'recentBroadcasts' => $recentBroadcasts,
            'stats' => [
                'students' => User::where('role', 'student')->where('is_active', true)->count(),
                'instructors' => User::where('role', 'instructor')->where('is_active', true)->count(),
            ],
        ]);
    }

    public function store(SendNotificationRequest $request, NotificationService $notificationService): RedirectResponse
    {
        $validated = $request->validated();

        $count = $notificationService->sendByAudience(
            audience: $validated['audience'],
            title: $validated['title'],
            message: $validated['message'],
            url: $validated['url'] ?? null,
            courseId: isset($validated['course_id']) ? (int) $validated['course_id'] : null,
        );

        ActivityLogService::log(
            $request->user()->id,
            'notification_broadcast',
            PushNotification::class,
            null,
            [
                'audience' => $validated['audience'],
                'title' => $validated['title'],
                'recipient_count' => $count,
            ],
            $request,
            "Gửi thông báo tới {$count} người dùng"
        );

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', "Đã gửi thông báo tới {$count} người dùng.");
    }
}
