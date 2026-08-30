<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\StudyGroup;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudyGroupController extends Controller
{
    public function index(Request $request): View
    {
        $studyGroups = $request->user()->studyGroups()
            ->with(['course:id,title,slug,thumbnail', 'creator:id,name'])
            ->withCount('members')
            ->latest('study_group_members.created_at')
            ->paginate(9)
            ->withQueryString();

        return view('student.dashboard.study-groups.index', compact('studyGroups'));
    }

    public function show(Request $request, StudyGroup $studyGroup): View
    {
        abort_unless($studyGroup->hasMember($request->user()->id), 403);

        $studyGroup->load([
            'course:id,title,slug,thumbnail',
            'creator:id,name,avatar',
            'members:id,name,avatar',
        ])->loadCount('members');

        return view('student.dashboard.study-groups.show', compact('studyGroup'));
    }
}
