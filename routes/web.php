<?php

use App\Http\Controllers\Web\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Web\Admin\ManageController;
use App\Http\Controllers\Web\Admin\RoleController;
use App\Http\Controllers\Web\Admin\UserController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CourseController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\Instructor\CourseController as InstructorCourseController;
use App\Http\Controllers\Web\Instructor\CurriculumController as InstructorCurriculumController;
use App\Http\Controllers\Web\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\Web\Instructor\QuizController as InstructorQuizController;
use App\Http\Controllers\Web\Student\CartController;
use App\Http\Controllers\Web\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Web\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Web\Student\MiscController as StudentMiscController;
use App\Http\Controllers\Web\Student\QuizController as StudentQuizController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::middleware('auth')->group(function () {
    Route::post('/courses/{course}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');
    Route::get('/my-courses', [StudentCourseController::class, 'index'])->name('my-courses');
});
Route::get('/courses/{course}/lessons/{lesson}', [CourseController::class, 'lesson'])->name('courses.lessons.show');
Route::post('/courses/{course}/lessons/{lesson}/progress', [CourseController::class, 'updateLessonProgress'])->middleware('auth')->name('courses.lessons.progress');
Route::get('/learn/{course:slug}/lessons/{lesson}/quiz', [StudentQuizController::class, 'show'])->name('learn.lessons.quiz.show');
Route::post('/learn/{course:slug}/lessons/{lesson}/quiz/submit', [StudentQuizController::class, 'submit'])->middleware('auth')->name('learn.lessons.quiz.submit');
Route::get('/courses/{slug}', [CourseController::class, 'show'])->name('courses.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    Route::get('/auth/{provider}/redirect', [AuthController::class, 'redirectToProvider'])->name('social.redirect');
    Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback'])->name('social.callback');
    Route::post('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('quick-login');
});

Route::get('/auth/availability', [AuthController::class, 'availability'])->middleware('throttle:30,1')->name('auth.availability');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/email/verify', [AuthController::class, 'verificationNotice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
        ->middleware('throttle:3,1')
        ->name('verification.send');
    Route::get('/two-factor-challenge', [AuthController::class, 'showTwoFactorChallenge'])->name('two-factor.challenge');
    Route::post('/two-factor-challenge', [AuthController::class, 'verifyTwoFactor'])->middleware('throttle:6,1')->name('two-factor.verify');
    Route::post('/two-factor-challenge/resend', [AuthController::class, 'resendTwoFactor'])->middleware('throttle:3,1')->name('two-factor.resend');
});

Route::middleware(['auth', 'active', 'verified', '2fa'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/email', [ProfileController::class, 'updateEmail'])->name('profile.email.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/profile/two-factor/send', [ProfileController::class, 'sendTwoFactorCode'])->name('profile.two-factor.send');
    Route::post('/profile/two-factor/enable', [ProfileController::class, 'enableTwoFactor'])->name('profile.two-factor.enable');
    Route::delete('/profile/two-factor', [ProfileController::class, 'disableTwoFactor'])->name('profile.two-factor.disable');
    Route::delete('/profile/sessions/others', [ProfileController::class, 'destroyOtherSessions'])->name('profile.sessions.destroy-others');
});

Route::get('/dashboard', function () {
    return redirect(auth()->user()->dashboardUrl());
})->middleware(['auth', 'active', 'verified', '2fa'])->name('dashboard');

// ─── HỌC VIÊN ───
Route::middleware(['auth', 'active', 'verified', '2fa', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/courses', [StudentCourseController::class, 'index'])->name('courses');
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add/{course}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{courseId}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::get('/wishlist', [StudentMiscController::class, 'wishlist'])->name('wishlist');
    Route::post('/wishlist/{courseId}', [StudentMiscController::class, 'toggleWishlist'])->name('wishlist.toggle');
    Route::get('/certificates', [StudentMiscController::class, 'certificates'])->name('certificates');
    Route::get('/orders', [StudentMiscController::class, 'orders'])->name('orders');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// ─── GIẢNG VIÊN ───
Route::middleware(['auth', 'active', 'verified', '2fa', 'role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/courses', [InstructorCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [InstructorCourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [InstructorCourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}/curriculum', [InstructorCurriculumController::class, 'index'])->name('courses.curriculum');
    Route::post('/courses/{course}/sections', [InstructorCurriculumController::class, 'storeSection'])->name('courses.sections.store');
    Route::put('/courses/{course}/sections/{section}', [InstructorCurriculumController::class, 'updateSection'])->name('courses.sections.update');
    Route::delete('/courses/{course}/sections/{section}', [InstructorCurriculumController::class, 'destroySection'])->name('courses.sections.destroy');
    Route::post('/courses/{course}/sections/{section}/lessons', [InstructorCurriculumController::class, 'storeLesson'])->name('courses.sections.lessons.store');
    Route::put('/courses/{course}/lessons/{lesson}', [InstructorCurriculumController::class, 'updateLesson'])->name('courses.lessons.update');
    Route::delete('/courses/{course}/lessons/{lesson}', [InstructorCurriculumController::class, 'destroyLesson'])->name('courses.lessons.destroy');
    Route::get('/courses/{course}/lessons/{lesson}/quiz', [InstructorQuizController::class, 'show'])->name('courses.lessons.quiz.show');
    Route::post('/courses/{course}/lessons/{lesson}/quiz', [InstructorQuizController::class, 'store'])->name('courses.lessons.quiz.store');
    Route::post('/quizzes/{quiz}/questions', [InstructorQuizController::class, 'storeQuestion'])->name('quizzes.questions.store');
    Route::put('/quiz-questions/{question}', [InstructorQuizController::class, 'updateQuestion'])->name('quiz-questions.update');
    Route::delete('/quiz-questions/{question}', [InstructorQuizController::class, 'destroyQuestion'])->name('quiz-questions.destroy');
    Route::post('/quiz-questions/{question}/answers', [InstructorQuizController::class, 'storeAnswer'])->name('quiz-questions.answers.store');
    Route::put('/quiz-questions/{question}/answers', [InstructorQuizController::class, 'updateAnswers'])->name('quiz-questions.answers.update');
    Route::put('/quiz-answers/{answer}', [InstructorQuizController::class, 'updateAnswer'])->name('quiz-answers.update');
    Route::delete('/quiz-answers/{answer}', [InstructorQuizController::class, 'destroyAnswer'])->name('quiz-answers.destroy');
    Route::get('/courses/{course}/edit', [InstructorCourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [InstructorCourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [InstructorCourseController::class, 'destroy'])->name('courses.destroy');
    Route::post('/courses/{course}/archive', [InstructorCourseController::class, 'archive'])->name('courses.archive');
    Route::post('/courses/{course}/chapters', [InstructorCourseController::class, 'addChapter'])->name('courses.chapters.store');
    Route::post('/courses/{course}/submit', [InstructorCourseController::class, 'submit'])->name('courses.submit');
    Route::get('/courses/{course}/students', [InstructorCourseController::class, 'students'])->name('courses.students');
    Route::post('/chapters/{chapter}/lessons', [InstructorCourseController::class, 'addLesson'])->name('chapters.lessons.store');
    Route::get('/revenue', [InstructorCourseController::class, 'revenue'])->name('revenue');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
});

// ─── ADMIN ───
Route::middleware(['auth', 'active', 'verified', '2fa', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::post('/users/bulk', [UserController::class, 'bulk'])->name('users.bulk');
    Route::get('/users/export/csv', [UserController::class, 'exportCsv'])->name('users.export.csv');
    Route::get('/users/export/pdf', [UserController::class, 'exportPdf'])->name('users.export.pdf');
    Route::post('/users/import', [UserController::class, 'import'])->name('users.import');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('/users/{user}/force', [UserController::class, 'forceDelete'])->name('users.force-delete');
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::get('/courses', [ManageController::class, 'index'])->name('courses.index');
    Route::get('/courses/pending', [ManageController::class, 'pendingCourses'])->name('courses.pending');
    Route::get('/courses/{course}/review', [ManageController::class, 'review'])->name('courses.review');
    Route::get('/courses/{course}/students', [ManageController::class, 'students'])->name('courses.students');
    Route::post('/courses/{course}/approve', [ManageController::class, 'approve'])->name('courses.approve');
    Route::post('/courses/{course}/reject', [ManageController::class, 'reject'])->name('courses.reject');
    Route::post('/courses/{course}/archive', [ManageController::class, 'archive'])->name('courses.archive');
    Route::post('/courses/{course}/restore', [ManageController::class, 'restore'])->name('courses.restore');
    Route::get('/courses/{course}', [ManageController::class, 'show'])->name('courses.show');
    Route::get('/revenue', [ManageController::class, 'revenue'])->name('revenue');
    Route::get('/activity-logs', [ManageController::class, 'activityLogs'])->name('activity-logs');
    Route::get('/homepage', [ManageController::class, 'homepage'])->name('homepage');
    Route::put('/homepage', [ManageController::class, 'updateHomepage'])->name('homepage.update');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
});
