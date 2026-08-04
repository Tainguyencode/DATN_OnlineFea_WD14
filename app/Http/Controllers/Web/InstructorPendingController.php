<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResubmitInstructorApplicationRequest;
use App\Models\InstructorProfile;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class InstructorPendingController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user || $user->role !== 'instructor') {
            return redirect()->route('home');
        }

        if ($user->instructor_status === 'approved') {
            return redirect()->route('instructor.dashboard');
        }

        $user->load('instructorProfile');

        return view('instructor.pending', [
            'user' => $user,
            'profile' => $user->instructorProfile,
        ]);
    }

    public function resubmit(ResubmitInstructorApplicationRequest $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->instructorProfile ?? new InstructorProfile(['user_id' => $user->id]);

        $validated = $request->validated();

        $cvPath = $profile->cv;
        if ($request->hasFile('cv')) {
            if ($cvPath && Storage::disk('public')->exists($cvPath)) {
                Storage::disk('public')->delete($cvPath);
            }
            $cvPath = $request->file('cv')->store('instructor_cvs', 'public');
        }

        $user->update([
            'phone' => $validated['phone'],
            'bio' => $validated['bio'],
            'instructor_status' => 'pending',
            'rejected_reason' => null,
        ]);

        $profile->fill([
            'phone' => $validated['phone'],
            'specialty' => $validated['specialty'],
            'experience' => $validated['experience'],
            'bio' => $validated['bio'],
            'linkedin_url' => $validated['linkedin_url'] ?? null,
            'github_url' => $validated['github_url'] ?? null,
            'website_url' => $validated['website_url'] ?? null,
            'cv' => $cvPath,
            'agree_information' => true,
            'agree_terms' => true,
        ])->save();

        ActivityLogService::log($user->id, 'resubmit_instructor_application', get_class($user), $user->id, [], $request);

        return redirect()->route('instructor.pending')
            ->with('success', 'Hồ sơ của bạn đã được cập nhật và gửi lại cho Ban quản trị xét duyệt.');
    }
}
