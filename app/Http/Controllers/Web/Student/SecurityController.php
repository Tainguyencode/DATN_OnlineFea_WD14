<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Web\ProfileController as BaseProfileController;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SecurityController extends BaseProfileController
{
    public function index(Request $request): View
    {
        return view('student.dashboard.profile.security', ['user' => $request->user()]);
    }
}
