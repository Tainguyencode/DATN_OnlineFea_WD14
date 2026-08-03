<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\StudyGroup;
use App\Models\StudyGroupMessage;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Controller Quản lý Nhóm học tập & Chat thời gian thực (Study Groups & Group Chat API/Web)
 * 
 * Chức năng chính:
 * 1. Tạo nhóm, quản lý danh sách nhóm học tập theo từng khóa học.
 * 2. Phân quyền tham gia (Chỉ học viên đã đăng ký khóa học hoặc Giảng viên/Admin mới được tham gia).
 * 3. Chat trong nhóm: Gửi tin nhắn văn bản, hình ảnh, video và tệp tài liệu đính kèm (PDF, Word, Excel, ZIP).
 * 4. Gửi thông báo Push Notification đến tất cả thành viên trong nhóm khi có tin nhắn mới.
 * 5. Bảo mật tệp tin chat: Tải về hoặc xem trực tuyến thông qua route bảo vệ quyền thành viên.
 */
class StudyGroupController extends Controller
{
    /**
     * Danh sách tất cả các nhóm học tập khả dụng.
     * 
     * @param Request $request Tham số lọc 'course_id' (nếu có)
     * @return mixed JSON data nếu gọi AJAX/API hoặc Render View nếu truy cập trực tiếp
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
     * 
     * @param Request $request Chứa course_id, name, description, max_members
     * @return mixed
     */
    public function store(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'max_members' => 'nullable|integer|min:1',
            ]);
        } else {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'max_members' => 'required|integer|min:2',
            ]);
        }

        $user = auth()->user();
        $courseId = $request->input('course_id');

        // Kiểm tra xem người dùng đã đăng ký khóa học hoặc là giảng viên/admin quản lý khóa học chưa
        $isEnrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->whereIn('status', [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_COMPLETED])
            ->exists();

        $course = Course::findOrFail($courseId);
        $isInstructorOrAdmin = $user->role === 'admin' || ($user->role === 'instructor' && $course->instructor_id === $user->id);

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
            'max_members' => $request->input('max_members') ?? 50,
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
     * 
     * @param StudyGroup $studyGroup Model nhóm học tập
     * @param Request $request
     * @return mixed
     */
    public function show(StudyGroup $studyGroup, Request $request)
    {
        $user = auth()->user();

        // Phân quyền: Phải là thành viên trong nhóm hoặc Admin hệ thống mới được vào xem
        if (!$studyGroup->hasMember($user->id) && $user->role !== 'admin') {
            $message = 'Bạn không phải là thành viên của nhóm này.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 403);
            }
            return redirect()->route('study-groups.index')->with('error', $message);
        }

        // Tự động đánh dấu các thông báo tin nhắn liên quan đến nhóm này là đã đọc
        try {
            $notificationUrl = route('study-groups.show', $studyGroup);
            $user->pushNotifications()
                ->where('is_read', false)
                ->where('type', 'study_group')
                ->where(function ($query) use ($notificationUrl, $studyGroup) {
                    $query->where('url', $notificationUrl)
                        ->orWhere('url', 'like', '%/study-groups/' . $studyGroup->id . '%');
                })
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Failed to mark study group notifications as read: " . $e->getMessage());
        }

        // Tải thông tin người tạo, các thành viên và lịch sử tin nhắn sắp xếp theo thời gian
        $studyGroup->load([
            'creator',
            'members',
            'messages' => function ($query) {
                $query->with('user')->orderBy('created_at', 'asc');
            }
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $studyGroup
            ]);
        }
        return view('student.study_groups.show', compact('studyGroup'));
    }

    /**
     * Gửi tin nhắn mới vào Nhóm học tập (Hỗ trợ Văn bản & File đính kèm Ảnh, Video, Tài liệu).
     * 
     * @param Request $request Chứa 'message' hoặc 'file'
     * @param StudyGroup $studyGroup
     * @return mixed
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

        // Validate nội dung tin nhắn và tệp tin đính kèm
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'message' => 'nullable|string',
            'file' => 'nullable|file|max:102400', // Tối đa 100MB tổng thể
        ]);

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

        if ($validator->fails()) {
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
                $notificationService = app(\App\Services\NotificationService::class);
                $title = "Tin nhắn mới trong nhóm " . $studyGroup->name;
                
                $bodyText = $user->name . ": ";
                if ($messageType === 'text') {
                    $bodyText .= \Illuminate\Support\Str::limit($request->input('message'), 60);
                } elseif ($messageType === 'image') {
                    $bodyText .= '[Hình ảnh]';
                } elseif ($messageType === 'video') {
                    $bodyText .= '[Video]';
                } else {
                    $bodyText .= '[Tệp đính kèm]';
                }

                $notificationUrl = route('study-groups.show', $studyGroup);

                $notificationService->sendToMany(
                    $otherMembers,
                    $title,
                    $bodyText,
                    'study_group',
                    $notificationUrl
                );
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send study group notification: " . $e->getMessage());
        }

        $messageText = 'Gửi tin nhắn thành công.';
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $messageText,
                'data' => $studyGroupMessage->load('user')
            ], 201);
        }

        return redirect()->route('study-groups.show', $studyGroup)->with('success', $messageText);
    }

    /**
     * Tải xuống hoặc Stream file phương tiện (Ảnh, Video, Tệp tin) bảo mật trong phòng chat.
     * 
     * @param StudyGroup $studyGroup Nhóm học tập
     * @param StudyGroupMessage $message Model tin nhắn chứa file
     */
    public function downloadFile(StudyGroup $studyGroup, StudyGroupMessage $message)
    {
        $user = auth()->user();

        // Kiểm tra quyền truy cập: Phải là thành viên nhóm hoặc Admin
        if (!$studyGroup->hasMember($user->id) && $user->role !== 'admin') {
            abort(403, 'Bạn không có quyền truy cập tệp tin này.');
        }

        // Đảm bảo tin nhắn thuộc đúng nhóm
        if ($message->study_group_id !== $studyGroup->id) {
            abort(404, 'Tệp tin không tìm thấy.');
        }

        // Kiểm tra file có thực sự tồn tại trong disk local hay không
        if (!$message->file_path || !\Illuminate\Support\Facades\Storage::disk('local')->exists($message->file_path)) {
            abort(404, 'Tệp tin không tồn tại trên hệ thống.');
        }

        $fullPath = \Illuminate\Support\Facades\Storage::disk('local')->path($message->file_path);

        // Với Ảnh & Video: Trả về dạng stream inline để hiển thị trực tiếp trên web/app
        if (in_array($message->message_type, ['video', 'image'])) {
            return response()->file($fullPath, [
                'Content-Type' => $message->mime_type,
                'Content-Disposition' => 'inline; filename="' . $message->file_name . '"'
            ]);
        }

        // Với các tệp tin khác (PDF, Word, ZIP): Trả về dạng download về máy
        return response()->download($fullPath, $message->file_name);
    }

    /**
     * Update the specified study group in storage.
     */
    public function update(Request $request, StudyGroup $studyGroup)
    {
        $user = auth()->user();

        // Check if user is creator or admin
        if ($user->id !== $studyGroup->creator_id && $user->role !== 'admin') {
            $message = 'Bạn không có quyền chỉnh sửa nhóm này.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 403);
            }
            return redirect()->back()->with('error', $message);
        }

        $currentMemberCount = $studyGroup->members()->count();

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_members' => 'nullable|integer|min:' . $currentMemberCount,
        ]);

        $studyGroup->update($request->only(['name', 'description', 'max_members']));

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
     * Remove the specified study group from storage.
     */
    public function destroy(StudyGroup $studyGroup, Request $request)
    {
        $user = auth()->user();

        // Check if user is creator or admin
        if ($user->id !== $studyGroup->creator_id && $user->role !== 'admin') {
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
     * Join the specified study group.
     */
    public function join(StudyGroup $studyGroup, Request $request)
    {
        $user = auth()->user();

        // Check if already a member
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

        // Check if the user is enrolled or has permission (instructor/admin)
        $isEnrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $studyGroup->course_id)
            ->whereIn('status', [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_COMPLETED])
            ->exists();

        $course = $studyGroup->course;
        $isInstructorOrAdmin = $user->role === 'admin' || ($user->role === 'instructor' && $course->instructor_id === $user->id);

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

        // Check if the group is full
        if ($studyGroup->isFull()) {
            $message = 'Nhóm học tập đã đạt số lượng thành viên tối đa.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }
            return redirect()->back()->with('error', $message);
        }

        $studyGroup->members()->attach($user->id, ['role' => 'member']);

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
     * Leave the specified study group.
     */
    public function leave(StudyGroup $studyGroup, Request $request)
    {
        $user = auth()->user();

        // Check if user is a member
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

        // Creator cannot leave (they should use delete to disband)
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
     * List all members of the specified study group.
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
     * Remove a member from the study group (Kick).
     */
    public function removeMember(StudyGroup $studyGroup, User $user, Request $request)
    {
        $currentUser = auth()->user();

        // Check if current user is group creator or admin
        if ($currentUser->id !== $studyGroup->creator_id && $currentUser->role !== 'admin') {
            $message = 'Bạn không có quyền xóa thành viên khỏi nhóm này.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 403);
            }
            return redirect()->back()->with('error', $message);
        }

        // Creator cannot remove themselves (they must delete the group)
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

        // Check if user is a member of the group
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

        // Detach the user from the group
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
}
