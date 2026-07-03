<?php

use App\Http\Controllers\Web\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Web\Admin\ManageController;
use App\Http\Controllers\Web\Admin\UserController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CourseController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\Instructor\CourseController as InstructorCourseController;
use App\Http\Controllers\Web\Instructor\CurriculumController as InstructorCurriculumController;
use App\Http\Controllers\Web\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\Web\Student\CartController;
use App\Http\Controllers\Web\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Web\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Web\Student\MiscController as StudentMiscController;
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
Route::get('/courses/{slug}', [CourseController::class, 'show'])->name('courses.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/dashboard', function () {
    return redirect(auth()->user()->dashboardUrl());
})->middleware('auth')->name('dashboard');

// ─── HỌC VIÊN ───
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
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
    Route::get('/profile', [StudentMiscController::class, 'profile'])->name('profile');
    Route::put('/profile', [StudentMiscController::class, 'updateProfile'])->name('profile.update');
});

// ─── GIẢNG VIÊN ───
Route::middleware(['auth', 'role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
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
    Route::get('/courses/{course}/edit', [InstructorCourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [InstructorCourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [InstructorCourseController::class, 'destroy'])->name('courses.destroy');
    Route::post('/courses/{course}/archive', [InstructorCourseController::class, 'archive'])->name('courses.archive');
    Route::post('/courses/{course}/chapters', [InstructorCourseController::class, 'addChapter'])->name('courses.chapters.store');
    Route::post('/courses/{course}/submit', [InstructorCourseController::class, 'submit'])->name('courses.submit');
    Route::get('/courses/{course}/students', [InstructorCourseController::class, 'students'])->name('courses.students');
    Route::post('/chapters/{chapter}/lessons', [InstructorCourseController::class, 'addLesson'])->name('chapters.lessons.store');
    Route::get('/revenue', [InstructorCourseController::class, 'revenue'])->name('revenue');
});

// ─── ADMIN ───
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
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
});
