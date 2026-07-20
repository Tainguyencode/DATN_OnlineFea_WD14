<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\StudyGroup;
use Illuminate\Http\Request;

class StudyGroupController extends Controller
{
    /**
     * Display a listing of the study groups.
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

        // Fetch courses the user is enrolled in, or if instructor/admin, courses they manage
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
     * Store a newly created study group in storage.
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

        // Check if the user is enrolled or has permission (instructor/admin)
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

        $studyGroup = StudyGroup::create([
            'course_id' => $courseId,
            'creator_id' => $user->id,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'max_members' => $request->input('max_members') ?? 50,
        ]);

        // Add creator as moderator member
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
     * Display the specified study group.
     */
    public function show(StudyGroup $studyGroup, Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $studyGroup->load(['creator', 'members'])
            ]);
        }
        return view('student.study_groups.show', compact('studyGroup'));
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
}
