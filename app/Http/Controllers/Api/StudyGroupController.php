<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\StudyGroupInvitationMail;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\StudyGroup;
use App\Models\StudyGroupInvitation;
use App\Models\StudyGroupMessage;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Controller Quản lý Nhóm học tập & Chat thời gian thực (Study Groups & Group Chat API/Web)
 */
class StudyGroupController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Danh sách tất cả các nhóm học tập khả dụng.
     */
    public function index(Request $request)
    {
        $query = StudyGroup::with(['creator', 'course']);

        if ($request->has('course_id')) {
            $query->where('course_id', $request->input('course_id'));
        }

        $studyGroups = $query->withCount('members')->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $studyGroups
            ]);
        }

        $user = auth()->user();

        // Lấy danh sách khóa học mà người dùng được phép tạo/tham gia nhóm
        if ($user->role === 'admin') {
            $availableCourses = Course::all();
        } elseif ($user->role === 'instructor') {
            $availableCourses = Course::where('instructor_id', $user->id)->get();
        } else {
            $availableCourses = Course::whereHas('enrollments', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->whereIn('status', [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_COMPLETED]);
            })->get();
        }

        return view('student.study_groups.index', compact('studyGroups', 'availableCourses'));
    }

    /**
     * Tạo một nhóm học tập mới cho khóa học.
     */
    public function store(Request $request)
    {
        $limitType = $request->input('max_members_type', 'custom');
        $rawMaxMembers = $request->input('max_members');

        // Validation rules
        $rules = [
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_members' => 'nullable',
        ];

        $messages = [
            'course_id.required' => 'Vui lòng chọn khóa học áp dụng.',
            'course_id.exists' => 'Khóa học không tồn tại.',
            'name.required' => 'Tên nhóm học tập không được để trống.',
            'name.max' => 'Tên nhóm không được vượt quá 255 ký tự.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        $maxMembers = null;
        if ($limitType === 'custom' || ($limitType !== 'unlimited' && $request->filled('max_members'))) {
            if ($request->filled('max_members')) {
                if (!is_numeric($rawMaxMembers) || (int) $rawMaxMembers != $rawMaxMembers || (int) $rawMaxMembers <= 0) {
                    $validator->errors()->add('max_members', 'Giới hạn thành viên phải là số nguyên dương.');
                } else {
                    $maxMembers = (int) $rawMaxMembers;
                }
            }
        }

        if ($validator->errors()->isNotEmpty()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = auth()->user();
        $courseId = $request->input('course_id');

        // Kiểm tra xem người dùng đã đăng ký khóa học hoặc là giảng viên/admin quản lý khóa học chưa
        $isEnrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->whereIn('status', [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_COMPLETED])
            ->exists();

        $course = Course::findOrFail($courseId);
        $isInstructorOrAdmin = $user->role === 'admin' || ($user->role === 'instructor' && (int) $course->instructor_id === (int) $user->id);

        if (!$isEnrolled && !$isInstructorOrAdmin) {
            $message = 'Bạn phải đăng ký khóa học này mới có thể lập nhóm học tập.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 403);
            }
            return redirect()->back()->with('error', $message);
        }

        // Khởi tạo nhóm học tập
        $studyGroup = StudyGroup::create([
            'course_id' => $courseId,
            'creator_id' => $user->id,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'max_members' => $maxMembers,
        ]);

        // Gán người tạo làm Trưởng nhóm (moderator)
        $studyGroup->members()->attach($user->id, ['role' => 'moderator']);

        $message = 'Tạo nhóm học tập thành công.';
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $studyGroup->load('members')
            ], 201);
        }
        return redirect()->route('study-groups.index')->with('success', $message);
    }

    /**
     * Xem chi tiết phòng chat của Nhóm học tập.
     */
    public function show(StudyGroup $studyGroup, Request $request)
    {
        $user = auth()->user();

        $pendingInvitation = null;
        // Phân quyền: Phải là thành viên trong nhóm, Admin hệ thống, hoặc người có lời mời đang chờ
        if (!$studyGroup->hasMember($user->id) && $user->role !== 'admin') {
            $pendingInvitation = $studyGroup->pendingInvitations()
                ->where('invited_user_id', $user->id)
                ->first();

            if (!$pendingInvitation) {
                $message = 'Bạn không phải là thành viên của nhóm này.';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 403);
                }
                return redirect()->route('study-groups.index')->with('error', $message);
            }
        }

        // Tự động đánh dấu các thông báo tin nhắn liên quan đến nhóm này là đã đọc
        try {
            $notificationUrl = route('study-groups.show', $studyGroup);
            $user->pushNotifications()
                ->where('is_read', false)
                ->whereIn('type', ['study_group', 'study_group_invitation'])
                ->where(function ($query) use ($notificationUrl, $studyGroup) {
                    $query->where('url', $notificationUrl)
                        ->orWhere('url', 'like', '%/study-groups/' . $studyGroup->id . '%');
                })
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
        } catch (\Throwable $e) {
            Log::error("Failed to mark study group notifications as read: " . $e->getMessage());
        }

        // Tải thông tin người tạo, các thành viên, lời mời đang chờ và lịch sử tin nhắn
        $studyGroup->load([
            'creator',
            'course',
            'members',
            'pendingInvitations.invitedUser',
            'pendingInvitations.inviter',
            'messages' => function ($query) {
                $query->with(['user', 'replyTo.user'])->orderBy('created_at', 'asc');
            }
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $studyGroup
            ]);
        }
        return view('student.study_groups.show', compact('studyGroup', 'pendingInvitation'));
    }

    /**
     * Gửi tin nhắn mới vào Nhóm học tập (Hỗ trợ Reply, Text, File, Image, Video).
     */
    public function storeMessage(Request $request, StudyGroup $studyGroup)
    {
        $user = auth()->user();

        // Phân quyền thành viên
        if (!$studyGroup->hasMember($user->id)) {
            $message = 'Bạn không phải là thành viên của nhóm này.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 403);
            }
            return redirect()->route('study-groups.index')->with('error', $message);
        }

        // Đảm bảo tương thích với input 'image' cũ
        if (!$request->hasFile('file') && $request->hasFile('image')) {
            $request->files->set('file', $request->file('image'));
        }

        // Validate nội dung tin nhắn, reply_to_message_id và tệp tin đính kèm
        $validator = Validator::make($request->all(), [
            'message' => 'nullable|string',
            'reply_to_message_id' => 'nullable|integer',
            'file' => 'nullable|file|max:102400', // Tối đa 100MB tổng thể
        ]);

        $replyToMessageId = $request->input('reply_to_message_id');
        $parentMessage = null;
        if ($replyToMessageId) {
            $parentMessage = StudyGroupMessage::where('id', $replyToMessageId)
                ->where('study_group_id', $studyGroup->id)
                ->first();

            if (!$parentMessage) {
                $validator->errors()->add('reply_to_message_id', 'Tin nhắn được chọn không thuộc nhóm này.');
            }
        }

        $validator->after(function ($validator) use ($request) {
            if (!$request->filled('message') && !$request->hasFile('file')) {
                $validator->errors()->add('file', 'Nội dung tin nhắn hoặc tệp tin đính kèm không được để trống.');
            }

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $mime = $file->getMimeType();
                $sizeKb = $file->getSize() / 1024;

                // Kiểm tra định dạng Ảnh (Max 5MB)
                if (str_starts_with($mime, 'image/')) {
                    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (!in_array($mime, $allowedMimes)) {
                        $validator->errors()->add('file', 'Hình ảnh phải có định dạng JPEG, PNG, GIF hoặc WEBP.');
                    }
                    if ($sizeKb > 5120) {
                        $validator->errors()->add('file', 'Kích thước hình ảnh tối đa là 5MB.');
                    }
                }
                // Kiểm tra định dạng Video (Max 100MB)
                elseif (str_starts_with($mime, 'video/')) {
                    $allowedMimes = ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska', 'video/webm'];
                    if (!in_array($mime, $allowedMimes)) {
                        $validator->errors()->add('file', 'Video phải có định dạng MP4, MOV, AVI, MKV hoặc WEBM.');
                    }
                    if ($sizeKb > 102400) {
                        $validator->errors()->add('file', 'Kích thước video tối đa là 100MB.');
                    }
                }
                // Kiểm tra tệp tài liệu khác (PDF, Word, Excel, ZIP - Max 20MB)
                else {
                    $allowedMimes = [
                        'application/pdf', 
                        'application/msword', 
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
                        'application/vnd.ms-excel', 
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
                        'application/zip', 
                        'application/x-zip-compressed',
                        'text/plain'
                    ];
                    if (!in_array($mime, $allowedMimes)) {
                        $validator->errors()->add('file', 'Định dạng tệp không được hỗ trợ (chỉ nhận PDF, Word, Excel, ZIP, TXT).');
                    }
                    if ($sizeKb > 20480) {
                        $validator->errors()->add('file', 'Kích thước tệp tin tối đa là 20MB.');
                    }
                }
            }
        });

        if ($validator->fails() || $validator->errors()->isNotEmpty()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $messageType = 'text';
        $fileName = null;
        $filePath = null;
        $mimeType = null;
        $fileSize = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();
            $fileSize = $file->getSize();

            if (str_starts_with($mimeType, 'image/')) {
                $messageType = 'image';
            } elseif (str_starts_with($mimeType, 'video/')) {
                $messageType = 'video';
            } else {
                $messageType = 'file';
            }

            // Lưu file an toàn trong bộ nhớ private (storage/app/chat)
            $filePath = $file->store('chat', 'local');
        }

        // Tạo tin nhắn mới trong CSDL
        $studyGroupMessage = $studyGroup->messages()->create([
            'user_id' => $user->id,
            'reply_to_message_id' => $parentMessage?->id,
            'message' => $request->input('message'),
            'message_type' => $messageType,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
        ]);

        // Gửi thông báo đẩy (Push Notification) đến các thành viên còn lại trong nhóm
        try {
            $otherMembers = $studyGroup->members()->where('users.id', '!=', $user->id)->get();
            if ($otherMembers->isNotEmpty()) {
                $title = "Tin nhắn mới trong nhóm " . $studyGroup->name;
                
                $bodyText = $user->name . ": ";
                if ($parentMessage) {
                    $bodyText = $user->name . " đã trả lời " . ($parentMessage->user?->name ?? 'thành viên') . ": ";
                }

                if ($messageType === 'text') {
                    $bodyText .= Str::limit($request->input('message'), 60);
                } elseif ($messageType === 'image') {
                    $bodyText .= '[Hình ảnh]';
                } elseif ($messageType === 'video') {
                    $bodyText .= '[Video]';
                } else {
                    $bodyText .= '[Tệp đính kèm]';
                }

                $notificationUrl = route('study-groups.show', $studyGroup);

                $this->notificationService->sendToMany(
                    $otherMembers,
                    $title,
                    $bodyText,
                    'study_group',
                    $notificationUrl
                );
            }
        } catch (\Throwable $e) {
            Log::error("Failed to send study group notification: " . $e->getMessage());
        }

        $studyGroupMessage->load(['user', 'replyTo.user']);

        $messageText = 'Gửi tin nhắn thành công.';
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $messageText,
                'data' => $studyGroupMessage
            ], 201);
        }

        return redirect()->route('study-groups.show', $studyGroup)->with('success', $messageText);
    }

    /**
     * Thu hồi tin nhắn của chính mình trong nhóm học tập.
     */
    public function recallMessage(StudyGroup $studyGroup, StudyGroupMessage $message, Request $request)
    {
        $user = auth()->user();

        // Kiểm tra quyền: Chỉ chính chủ tin nhắn hoặc Admin mới được thu hồi
        if ((int) $message->user_id !== (int) $user->id && $user->role !== 'admin') {
            $errorMessage = 'Bạn không có quyền thu hồi tin nhắn này.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 403);
            }
            return redirect()->back()->with('error', $errorMessage);
        }

        // Đảm bảo tin nhắn thuộc đúng nhóm
        if ($message->study_group_id !== $studyGroup->id) {
            $errorMessage = 'Tin nhắn không thuộc nhóm học tập này.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 404);
            }
            return redirect()->back()->with('error', $errorMessage);
        }

        // Xóa file vật lý đính kèm nếu có
        if ($message->file_path && Storage::disk('local')->exists($message->file_path)) {
            Storage::disk('local')->delete($message->file_path);
        }
        if ($message->image_path && Storage::disk('public')->exists($message->image_path)) {
            Storage::disk('public')->delete($message->image_path);
        }

        // Cập nhật trạng thái thu hồi
        $message->update([
            'is_recalled' => true,
            'message' => 'Tin nhắn đã được thu hồi',
            'image_path' => null,
            'file_path' => null,
            'file_name' => null,
            'mime_type' => null,
            'file_size' => null,
        ]);

        $message->load(['user', 'replyTo.user']);

        $successMessage = 'Đã thu hồi tin nhắn thành công.';
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'data' => $message
            ]);
        }

        return redirect()->back()->with('success', $successMessage);
    }

    /**
     * Tải xuống hoặc Stream file phương tiện bảo mật trong phòng chat.
     */
    public function downloadFile(StudyGroup $studyGroup, StudyGroupMessage $message)
    {
        $user = auth()->user();

        // Kiểm tra quyền truy cập: Phải là thành viên nhóm hoặc Admin
        if (!$studyGroup->hasMember($user->id) && $user->role !== 'admin') {
            abort(403, 'Bạn không có quyền truy cập tệp tin này.');
        }

        // Nếu tin nhắn đã thu hồi thì không cho tải file
        if ($message->is_recalled) {
            abort(404, 'Tin nhắn đã được thu hồi.');
        }

        // Đảm bảo tin nhắn thuộc đúng nhóm
        if ($message->study_group_id !== $studyGroup->id) {
            abort(404, 'Tệp tin không tìm thấy.');
        }

        // Kiểm tra file có thực sự tồn tại trong disk local hay không
        if (!$message->file_path || !Storage::disk('local')->exists($message->file_path)) {
            abort(404, 'Tệp tin không tồn tại trên hệ thống.');
        }

        $fullPath = Storage::disk('local')->path($message->file_path);

        // Với Ảnh & Video: Trả về dạng stream inline
        if (in_array($message->message_type, ['video', 'image'])) {
            return response()->file($fullPath, [
                'Content-Type' => $message->mime_type,
                'Content-Disposition' => 'inline; filename="' . $message->file_name . '"'
            ]);
        }

        // Với các tệp tin khác: Trả về dạng download
        return response()->download($fullPath, $message->file_name);
    }

    /**
     * Cập nhật thông tin & Giới hạn thành viên nhóm.
     * Chỉ Trưởng nhóm (Owner), Giảng viên phụ trách khóa học, hoặc Admin mới được phép.
     */
    public function update(Request $request, StudyGroup $studyGroup)
    {
        $user = auth()->user();

        // Kiểm tra quyền: Trưởng nhóm, Giảng viên khóa học, hoặc Admin
        if (!$studyGroup->canManage($user)) {
            $message = 'Bạn không có quyền thay đổi giới hạn thành viên của nhóm.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 403);
            }
            return redirect()->back()->with('error', $message);
        }

        $currentMemberCount = $studyGroup->members()->count();
        $limitType = $request->input('max_members_type', 'custom');
        $rawMaxMembers = $request->input('max_members');

        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_members' => 'nullable',
        ];

        $messages = [
            'name.required' => 'Tên nhóm học tập không được để trống.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        $maxMembers = null;
        if ($limitType === 'custom' || ($limitType !== 'unlimited' && $request->filled('max_members'))) {
            if ($request->filled('max_members')) {
                if (!is_numeric($rawMaxMembers) || (int) $rawMaxMembers != $rawMaxMembers || (int) $rawMaxMembers <= 0) {
                    $validator->errors()->add('max_members', 'Giới hạn thành viên phải là số nguyên dương.');
                } else {
                    $maxMembers = (int) $rawMaxMembers;
                    if ($maxMembers < $currentMemberCount) {
                        $validator->errors()->add('max_members', 'Không thể đặt giới hạn thấp hơn số thành viên hiện tại của nhóm.');
                    }
                }
            }
        }

        if ($validator->errors()->isNotEmpty()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $studyGroup->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'max_members' => $maxMembers,
        ]);

        $message = 'Cập nhật thông tin nhóm thành công.';
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $studyGroup->load('members')
            ]);
        }
        return redirect()->back()->with('success', $message);
    }

    /**
     * Xóa nhóm học tập.
     */
    public function destroy(StudyGroup $studyGroup, Request $request)
    {
        $user = auth()->user();

        if (!$studyGroup->canManage($user)) {
            $message = 'Bạn không có quyền xóa nhóm này.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 403);
            }
            return redirect()->back()->with('error', $message);
        }

        $studyGroup->delete();

        $message = 'Xóa nhóm học tập thành công.';
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }
        return redirect()->route('study-groups.index')->with('success', $message);
    }

    /**
     * Tham gia nhóm học tập trực tiếp.
     */
    public function join(StudyGroup $studyGroup, Request $request)
    {
        $user = auth()->user();

        // Kiểm tra xem đã là thành viên chưa
        if ($studyGroup->hasMember($user->id)) {
            $message = 'Bạn đã là thành viên của nhóm này rồi.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }
            return redirect()->back()->with('error', $message);
        }

        // Kiểm tra quyền đăng ký khóa học hoặc Giảng viên/Admin
        $isEnrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $studyGroup->course_id)
            ->whereIn('status', [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_COMPLETED])
            ->exists();

        $course = $studyGroup->course;
        $isInstructorOrAdmin = $user->role === 'admin' || ($user->role === 'instructor' && (int) $course->instructor_id === (int) $user->id);

        if (!$isEnrolled && !$isInstructorOrAdmin) {
            $message = 'Bạn phải đăng ký khóa học này mới có thể tham gia nhóm.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 403);
            }
            return redirect()->back()->with('error', $message);
        }

        // Kiểm tra giới hạn thành viên
        if ($studyGroup->isFull()) {
            $message = 'Nhóm đã đạt giới hạn thành viên.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }
            return redirect()->back()->with('error', $message);
        }

        $studyGroup->members()->attach($user->id, ['role' => 'member']);

        // Đánh dấu accepted nếu có lời mời đang pending
        $studyGroup->pendingInvitations()
            ->where('invited_user_id', $user->id)
            ->update([
                'status' => StudyGroupInvitation::STATUS_ACCEPTED,
                'accepted_at' => now(),
            ]);

        $message = 'Tham gia nhóm học tập thành công.';
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $studyGroup->load('members')
            ]);
        }
        return redirect()->back()->with('success', $message);
    }

    /**
     * Rời nhóm học tập.
     */
    public function leave(StudyGroup $studyGroup, Request $request)
    {
        $user = auth()->user();

        if (!$studyGroup->hasMember($user->id)) {
            $message = 'Bạn không phải là thành viên của nhóm này.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }
            return redirect()->back()->with('error', $message);
        }

        // Creator không thể rời nhóm
        if ($user->id === $studyGroup->creator_id) {
            $message = 'Người tạo nhóm không thể rời nhóm. Hãy xóa nhóm nếu muốn giải tán.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }
            return redirect()->back()->with('error', $message);
        }

        $studyGroup->members()->detach($user->id);

        $message = 'Rời nhóm học tập thành công.';
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }
        return redirect()->back()->with('success', $message);
    }

    /**
     * Danh sách thành viên nhóm.
     */
    public function members(StudyGroup $studyGroup)
    {
        $members = $studyGroup->members;

        return response()->json([
            'success' => true,
            'data' => $members
        ]);
    }

    /**
     * Xóa thành viên khỏi nhóm (Kick).
     */
    public function removeMember(StudyGroup $studyGroup, User $user, Request $request)
    {
        $currentUser = auth()->user();

        if (!$studyGroup->canManage($currentUser)) {
            $message = 'Bạn không có quyền xóa thành viên khỏi nhóm này.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 403);
            }
            return redirect()->back()->with('error', $message);
        }

        // Không thể xóa chính Trưởng nhóm
        if ($user->id === $studyGroup->creator_id) {
            $message = 'Trưởng nhóm không thể bị xóa khỏi nhóm. Hãy xóa nhóm nếu muốn giải tán.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }
            return redirect()->back()->with('error', $message);
        }

        if (!$studyGroup->hasMember($user->id)) {
            $message = 'Người này không phải là thành viên của nhóm.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }
            return redirect()->back()->with('error', $message);
        }

        $studyGroup->members()->detach($user->id);

        $message = "Đã xóa thành viên {$user->name} ra khỏi nhóm.";
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }
        return redirect()->back()->with('success', $message);
    }

    /**
     * Tìm kiếm người dùng bằng email hoặc username để mời vào nhóm.
     */
    public function searchUsers(Request $request, StudyGroup $studyGroup)
    {
        $user = auth()->user();

        if (!$studyGroup->canManage($user) && !$studyGroup->hasMember($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền tìm kiếm thành viên cho nhóm này.'
            ], 403);
        }

        $query = trim((string) $request->input('q', ''));
        if ($query === '') {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        $users = User::query()
            ->where(function ($q) use ($query) {
                $q->where('email', $query)
                  ->orWhere('username', $query)
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('username', 'like', "%{$query}%")
                  ->orWhere('name', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();

        $result = $users->map(function (User $u) use ($studyGroup, $user) {
            $isSelf = (int) $u->id === (int) $user->id;
            $isMember = $studyGroup->hasMember($u->id);
            $hasPendingInvite = $studyGroup->pendingInvitations()->where('invited_user_id', $u->id)->exists();

            return [
                'id' => $u->id,
                'name' => $u->name,
                'username' => $u->username ?? Str::slug($u->name, ''),
                'email' => $u->email,
                'avatar_url' => $u->avatarUrl(),
                'is_self' => $isSelf,
                'is_member' => $isMember,
                'has_pending_invite' => $hasPendingInvite,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * Gửi lời mời tham gia nhóm học tập.
     */
    public function invite(Request $request, StudyGroup $studyGroup)
    {
        $currentUser = auth()->user();

        // Kiểm tra quyền mời: Chỉ Trưởng nhóm (Owner), Giảng viên, hoặc Admin
        if (!$studyGroup->canManage($currentUser)) {
            $message = 'Bạn không có quyền mời thành viên vào nhóm này.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 403);
            }
            return redirect()->back()->with('error', $message);
        }

        // Tìm kiếm target user theo user_id hoặc email/username
        $targetUser = null;
        if ($request->filled('user_id')) {
            $targetUser = User::find($request->input('user_id'));
        } elseif ($request->filled('identifier')) {
            $identifier = trim((string) $request->input('identifier'));
            $targetUser = User::where('email', $identifier)
                ->orWhere('username', $identifier)
                ->first();
        }

        if (!$targetUser) {
            $message = 'Người dùng không tồn tại.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 404);
            }
            return redirect()->back()->with('error', $message);
        }

        // Không cho mời chính mình
        if ((int) $targetUser->id === (int) $currentUser->id) {
            $message = 'Bạn không thể mời chính mình.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }
            return redirect()->back()->with('error', $message);
        }

        // Không cho mời người đã là member
        if ($studyGroup->hasMember($targetUser->id)) {
            $message = 'Người dùng này đã là thành viên của nhóm.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }
            return redirect()->back()->with('error', $message);
        }

        // Không tạo duplicate invitation pending
        $hasPending = $studyGroup->pendingInvitations()
            ->where('invited_user_id', $targetUser->id)
            ->exists();

        if ($hasPending) {
            $message = 'Lời mời đang chờ người dùng xác nhận.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }
            return redirect()->back()->with('error', $message);
        }

        // Kiểm tra giới hạn thành viên
        if ($studyGroup->isFull()) {
            $message = 'Nhóm đã đạt giới hạn thành viên.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }
            return redirect()->back()->with('error', $message);
        }

        // Tạo invitation record
        $invitation = StudyGroupInvitation::create([
            'study_group_id' => $studyGroup->id,
            'invited_user_id' => $targetUser->id,
            'invited_by' => $currentUser->id,
            'status' => StudyGroupInvitation::STATUS_PENDING,
            'expires_at' => now()->addDays(7),
        ]);

        // Gửi thông báo đến người được mời
        try {
            $title = 'Bạn được mời vào nhóm học';
            $notificationMessage = "{$currentUser->name} đã mời bạn tham gia nhóm \"{$studyGroup->name}\".";
            $url = route('study-groups.show', $studyGroup);

            $this->notificationService->send(
                $targetUser,
                $title,
                $notificationMessage,
                'study_group_invitation',
                $url
            );
        } catch (\Throwable $e) {
            Log::error("Failed to send study group invitation notification: " . $e->getMessage());
        }

        // Gửi email mời tham gia nhóm học tập trực tiếp tới hộp thư email của người được mời
        try {
            if ($targetUser->email) {
                Mail::to($targetUser->email)->send(
                    new StudyGroupInvitationMail(
                        invitedUser: $targetUser,
                        inviter: $currentUser,
                        studyGroup: $studyGroup,
                        invitation: $invitation,
                        actionUrl: route('study-groups.show', $studyGroup)
                    )
                );
            }
        } catch (\Throwable $e) {
            Log::error("Failed to send study group invitation email: " . $e->getMessage());
        }

        $successMessage = "Đã gửi lời mời tham gia nhóm tới {$targetUser->name}.";
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'data' => $invitation->load(['invitedUser', 'inviter'])
            ], 201);
        }

        return redirect()->back()->with('success', $successMessage);
    }

    /**
     * Hủy lời mời đang pending.
     */
    public function cancelInvitation(StudyGroup $studyGroup, StudyGroupInvitation $invitation, Request $request)
    {
        $currentUser = auth()->user();

        if (!$studyGroup->canManage($currentUser)) {
            $message = 'Bạn không có quyền hủy lời mời này.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 403);
            }
            return redirect()->back()->with('error', $message);
        }

        if ($invitation->study_group_id !== $studyGroup->id) {
            abort(404, 'Lời mời không tồn tại.');
        }

        $invitation->update([
            'status' => StudyGroupInvitation::STATUS_CANCELLED,
        ]);

        $successMessage = 'Đã hủy lời mời thành công.';
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage
            ]);
        }

        return redirect()->back()->with('success', $successMessage);
    }

    /**
     * Chấp nhận lời mời tham gia nhóm học tập.
     */
    public function acceptInvitation(StudyGroupInvitation $invitation, Request $request)
    {
        $user = auth()->user();

        if ((int) $invitation->invited_user_id !== (int) $user->id) {
            $message = 'Bạn không có quyền thao tác trên lời mời này.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 403);
            }
            return redirect()->route('study-groups.index')->with('error', $message);
        }

        if ($invitation->status !== StudyGroupInvitation::STATUS_PENDING) {
            $message = 'Lời mời này không còn hiệu lực.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }
            return redirect()->route('study-groups.index')->with('error', $message);
        }

        $studyGroup = $invitation->studyGroup;

        // BẮT BUỘC: Kiểm tra lại giới hạn thành viên tại thời điểm accept
        if ($studyGroup->isFull()) {
            $message = 'Nhóm đã đạt giới hạn thành viên.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }
            return redirect()->route('study-groups.index')->with('error', $message);
        }

        if (!$studyGroup->hasMember($user->id)) {
            $studyGroup->members()->attach($user->id, ['role' => 'member']);
        }

        $invitation->update([
            'status' => StudyGroupInvitation::STATUS_ACCEPTED,
            'accepted_at' => now(),
        ]);

        $successMessage = 'Bạn đã tham gia nhóm học tập thành công.';
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'data' => $studyGroup->load('members')
            ]);
        }

        return redirect()->route('study-groups.show', $studyGroup)->with('success', $successMessage);
    }

    /**
     * Từ chối lời mời tham gia nhóm học tập.
     */
    public function rejectInvitation(StudyGroupInvitation $invitation, Request $request)
    {
        $user = auth()->user();

        if ((int) $invitation->invited_user_id !== (int) $user->id) {
            $message = 'Bạn không có quyền thao tác trên lời mời này.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 403);
            }
            return redirect()->route('study-groups.index')->with('error', $message);
        }

        $invitation->update([
            'status' => StudyGroupInvitation::STATUS_REJECTED,
        ]);

        $successMessage = 'Đã từ chối lời mời tham gia nhóm.';
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage
            ]);
        }

        return redirect()->route('study-groups.index')->with('success', $successMessage);
    }
}
