<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Web\ProfileController as BaseProfileController;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends BaseProfileController
{
    public function edit(Request $request): View
    {
        return view('student.dashboard.profile.edit', ['user' => $request->user()]);
    }
}
