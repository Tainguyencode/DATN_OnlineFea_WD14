<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\DiscussionChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessengerController extends Controller
{
    public function __construct(private readonly DiscussionChatService $chat) {}

    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->isStudent() || $request->user()->isInstructor() || $request->user()->isAdmin(), 403);
        $conversations = $this->chat->conversations($request->user(), $request->integer('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $conversations->items(),
            'meta' => [
                'current_page' => $conversations->currentPage(),
                'last_page' => $conversations->lastPage(),
                'total' => $conversations->total(),
                'unread_total' => $this->chat->totalUnread($request->user()),
            ],
        ]);
    }
}
