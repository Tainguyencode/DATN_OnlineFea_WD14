<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreInstructorApplicationRequest;
use App\Models\InstructorApplication;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InstructorApplicationController extends Controller
{
    public function create(): View|RedirectResponse
    {
        $user = auth()->user();

        if (! $user->isStudent()) {
            return redirect($user->dashboardUrl());
        }

        if ($user->instructorApplication()->exists()) {
            return redirect()->route('student.dashboard')
                ->with('info', 'Bạn đã gửi đơn đăng ký giảng viên. Vui lòng chờ admin duyệt.');
        }

        return view('instructor.become', ['user' => $user]);
    }

    public function store(StoreInstructorApplicationRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->instructorApplication()->exists()) {
            return back()->with('error', 'Bạn đã gửi đơn đăng ký.');
        }

        $cvPath = $request->file('cv')->store('instructor-applications/cv', 'public');
        $certificatePath = $request->hasFile('certificate')
            ? $request->file('certificate')->store('instructor-applications/certificates', 'public')
            : null;

        if ($request->hasFile('avatar')) {
            $user->update(['avatar' => $request->file('avatar')->store('avatars', 'public')]);
        }

        InstructorApplication::create([
            'user_id' => $user->id,
            'expertise' => $request->input('expertise'),
            'experience' => $request->input('experience'),
            'introduction' => $request->input('introduction'),
            'cv_path' => $cvPath,
            'certificate_path' => $certificatePath,
            'intro_video_url' => $request->input('intro_video_url'),
            'bank_name' => $request->input('bank_name'),
            'bank_account_number' => $request->input('bank_account_number'),
            'bank_account_name' => $request->input('bank_account_name'),
            'status' => 'pending',
        ]);

        ActivityLogService::log($user->id, 'instructor_application_submitted', User::class, $user->id, null, $request);

        return redirect()->route('student.dashboard')
            ->with('success', 'Đơn đăng ký giảng viên đã được gửi. Admin sẽ xem xét trong thời gian sớm nhất.');
    }
}
