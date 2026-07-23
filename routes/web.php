<?php

use App\Http\Controllers\Web\Admin\AiModerationController;
use App\Http\Controllers\Web\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Web\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Web\Admin\CourseReviewController;
use App\Http\Controllers\Web\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Web\Admin\ManageController;
use App\Http\Controllers\Web\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Web\Admin\RoleController;
use App\Http\Controllers\Web\Admin\StudentReviewController as AdminStudentReviewController;
use App\Http\Controllers\Web\Admin\UserController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CourseController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\Instructor\CourseController as InstructorCourseController;
use App\Http\Controllers\Web\Instructor\CurriculumController as InstructorCurriculumController;
use App\Http\Controllers\Web\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\Web\Instructor\QuizController as InstructorQuizController;
use App\Http\Controllers\Web\Instructor\ReviewController as InstructorReviewController;
use App\Http\Controllers\Web\NotificationController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\ReviewController;
use App\Http\Controllers\Web\ReviewHelpfulController;
use App\Http\Controllers\Web\SocialAuthController;
use App\Http\Controllers\Web\Student\CartController;
use App\Http\Controllers\Web\Student\MiscController as StudentMiscController;
use App\Http\Controllers\Web\Student\QuizController as StudentQuizController;
use App\Http\Controllers\Web\Student\RecentlyViewedCourseController;
use App\Http\Controllers\Web\Student\ReviewController as StudentReviewController;
use App\Http\Controllers\Api\StudyGroupController;
use App\Models\User;
use App\Services\GeminiService;
use App\Services\VideoFrameExtractor;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

if (app()->environment('local')) {
    Route::get('/test-frame', function (VideoFrameExtractor $extractor) {
        $frames = $extractor->extract(
            storage_path('app/public/lesson-videos/N3KN3TMzv1u4QWYDJI0NEPxqdeJqz1HfRW5Rnn8L.mp4')
        );

        return $frames;
    });

    Route::get('/test-gemini', function (GeminiService $gemini) {
        $framePath = storage_path('app'.DIRECTORY_SEPARATOR.'temp_frames'.DIRECTORY_SEPARATOR.'frame_0.jpg');

        $result = $gemini->analyzeImage($framePath);

        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    });
}

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/category/{category:slug}', [CourseController::class, 'category'])->name('courses.category');
Route::middleware(['auth', 'active', 'verified'])->group(function () {
    Route::post('/courses/{course}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');
    Route::get('/my-courses', fn () => redirect(route('student.dashboard').'#courses'))->name('my-courses');

    // Study Groups
    Route::get('/study-groups', [StudyGroupController::class, 'index'])->name('study-groups.index');
    Route::post('/study-groups', [StudyGroupController::class, 'store'])->name('study-groups.store');
    Route::get('/study-groups/{studyGroup}', [StudyGroupController::class, 'show'])->name('study-groups.show');
    Route::put('/study-groups/{studyGroup}', [StudyGroupController::class, 'update'])->name('study-groups.update');
    Route::delete('/study-groups/{studyGroup}', [StudyGroupController::class, 'destroy'])->name('study-groups.destroy');
    Route::post('/study-groups/{studyGroup}/join', [StudyGroupController::class, 'join'])->name('study-groups.join');
    Route::post('/study-groups/{studyGroup}/leave', [StudyGroupController::class, 'leave'])->name('study-groups.leave');
    Route::get('/study-groups/{studyGroup}/members', [StudyGroupController::class, 'members'])->name('study-groups.members');
    Route::post('/study-groups/{studyGroup}/messages', [StudyGroupController::class, 'storeMessage'])->name('study-groups.messages.store');
    Route::delete('/study-groups/{studyGroup}/members/{user}', [StudyGroupController::class, 'removeMember'])->name('study-groups.members.remove');
});
Route::middleware(['auth', 'active', 'role:student'])->group(function () {
    Route::get('/favorites', [StudentMiscController::class, 'wishlist'])->name('favorites.index');
    Route::post('/courses/{course}/favorite', [StudentMiscController::class, 'storeFavorite'])->name('courses.favorite.store');
    Route::delete('/courses/{course}/favorite', [StudentMiscController::class, 'destroyFavorite'])->name('courses.favorite.destroy');
});
Route::get('/courses/{course}/lessons/{lesson}', [CourseController::class, 'lesson'])->name('courses.lessons.show');
Route::post('/courses/{course}/lessons/{lesson}/progress', [CourseController::class, 'updateLessonProgress'])->middleware('auth')->name('courses.lessons.progress');
Route::post('/courses/{course}/lessons/{lesson}/quiz/submit', [StudentQuizController::class, 'submitAjax'])->middleware('auth')->name('courses.lessons.quiz.submit');

Route::get('/learn/{course:slug}/lessons/{lesson}/quiz', [StudentQuizController::class, 'show'])->name('learn.lessons.quiz.show');
Route::post('/learn/{course:slug}/lessons/{lesson}/quiz/submit', [StudentQuizController::class, 'submit'])->middleware('auth')->name('learn.lessons.quiz.submit');
Route::middleware(['auth', 'active', 'verified', 'role:student', 'throttle:6,1'])->group(function () {
    Route::post('/courses/{course}/reviews', [ReviewController::class, 'store'])->name('courses.reviews.store');
    Route::put('/courses/{course}/reviews/{review}', [ReviewController::class, 'update'])->name('courses.reviews.update');
    Route::delete('/courses/{course}/reviews/{review}', [ReviewController::class, 'destroy'])->name('courses.reviews.destroy');
});
Route::post('/reviews/{review}/helpful', [ReviewHelpfulController::class, 'toggle'])
    ->middleware(['auth', 'active', 'verified', 'throttle:20,1'])
    ->name('reviews.helpful.toggle');
Route::get('/courses/{slug}', [CourseController::class, 'show'])->name('courses.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::get('/register/{role}', [AuthController::class, 'showRegisterRole'])->where('role', 'student|instructor')->name('register.role');
    Route::post('/register/{role}', [AuthController::class, 'register'])->where('role', 'student|instructor')->middleware('throttle:6,1');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
        ->whereIn('provider', ['google', 'facebook'])
        ->name('social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
        ->whereIn('provider', ['google', 'facebook'])
        ->name('social.callback');
    if (app()->environment('local')) {
        Route::post('/quick-login/{role}', [AuthController::class, 'quickLogin'])
            ->whereIn('role', ['admin', 'instructor', 'student'])
            ->name('quick-login');
    }
});

Route::get('/auth/availability', [AuthController::class, 'availability'])->middleware('throttle:30,1')->name('auth.availability');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/email/verify', [AuthController::class, 'verificationNotice'])->name('verification.notice');
    Route::post('/email/verify-code', [AuthController::class, 'verifyEmailCode'])
        ->middleware('throttle:10,1')
        ->name('verification.code.verify');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
        ->middleware('throttle:5,15')
        ->name('verification.send');
    Route::post('/email/verify/instant', [AuthController::class, 'instantVerify'])
        ->name('verification.instant');
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

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});

Route::get('/dashboard', function () {
    return redirect(auth()->user()->dashboardUrl());
})->middleware(['auth', 'active', 'verified', '2fa'])->name('dashboard');

// ─── HỌC VIÊN ───
Route::middleware(['auth', 'active', 'verified', '2fa', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'studentDashboard'])->name('dashboard');
    Route::get('/courses', fn () => redirect(route('student.dashboard').'#courses'))->name('courses');
    Route::get('/recently-viewed-courses', [RecentlyViewedCourseController::class, 'index'])->name('recently-viewed.index');
    Route::get('/reviews', [StudentReviewController::class, 'index'])->name('reviews.index');
    Route::delete('/recently-viewed-courses', [RecentlyViewedCourseController::class, 'clear'])->name('recently-viewed.clear');
    Route::delete('/recently-viewed-courses/{recentlyViewedCourse}', [RecentlyViewedCourseController::class, 'destroy'])->name('recently-viewed.destroy');
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add/{course}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{courseId}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/cart/coupon/apply', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
    Route::get('/checkout/{order_code}/pay', [CartController::class, 'showPaymentPage'])->name('checkout.pay');
    Route::get('/checkout/mock-gateway/{order_code}', [CartController::class, 'mockGateway'])->name('checkout.mock_gateway');
    Route::post('/checkout/{order_code}/simulate', [CartController::class, 'simulatePayment'])->name('checkout.simulate');
    Route::get('/checkout/{order_code}/success', [CartController::class, 'successPage'])->name('checkout.success');
    Route::get('/checkout/{order_code}/failed', [CartController::class, 'failedPage'])->name('checkout.failed');
    Route::get('/wishlist', fn () => redirect(route('student.dashboard').'#wishlist'))->name('wishlist');
    Route::post('/wishlist/{courseId}', [StudentMiscController::class, 'toggleWishlist'])->name('wishlist.toggle');
    Route::get('/certificates', fn () => redirect(route('student.dashboard').'#certificates'))->name('certificates');
    Route::get('/certificates/{certificate}/pdf', [StudentMiscController::class, 'viewCertificatePdf'])->name('certificates.pdf');
    Route::get('/orders', fn () => redirect(route('student.dashboard').'#orders'))->name('orders');
    Route::get('/profile', [ProfileController::class, 'studentShow'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

// ─── GIẢNG VIÊN ───
Route::middleware(['auth', 'active', '2fa', 'role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/courses', [InstructorCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [InstructorCourseController::class, 'create'])->name('courses.create');
    Route::get('/courses/{course}/curriculum', [InstructorCurriculumController::class, 'index'])->name('courses.curriculum');
    Route::get('/courses/{course}/lessons/{lesson}/quiz', [InstructorQuizController::class, 'show'])->name('courses.lessons.quiz.show');
    Route::get('/courses/{course}/edit', [InstructorCourseController::class, 'edit'])->name('courses.edit');
    Route::get('/courses/{course}/students', [InstructorCourseController::class, 'students'])->name('courses.students');
    Route::get('/revenue', [InstructorCourseController::class, 'revenue'])->name('revenue');
    Route::get('/reviews', [InstructorReviewController::class, 'index'])->name('reviews.index');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::middleware('verified')->group(function () {
        Route::post('/courses/{course}/reviews/{review}/reply', [InstructorReviewController::class, 'reply'])->middleware('throttle:12,1')->name('reviews.reply');
        Route::put('/courses/{course}/reviews/{review}/reply', [InstructorReviewController::class, 'reply'])->middleware('throttle:12,1')->name('reviews.reply.update');
        Route::delete('/courses/{course}/reviews/{review}/reply', [InstructorReviewController::class, 'destroyReply'])->name('reviews.reply.destroy');
        Route::post('/courses', [InstructorCourseController::class, 'store'])->name('courses.store');
        Route::post('/courses/{course}/sections', [InstructorCurriculumController::class, 'storeSection'])->name('courses.sections.store');
        Route::put('/courses/{course}/sections/{section}', [InstructorCurriculumController::class, 'updateSection'])->name('courses.sections.update');
        Route::delete('/courses/{course}/sections/{section}', [InstructorCurriculumController::class, 'destroySection'])->name('courses.sections.destroy');
        Route::post('/courses/{course}/sections/{section}/lessons', [InstructorCurriculumController::class, 'storeLesson'])->name('courses.sections.lessons.store');
        Route::put('/courses/{course}/lessons/{lesson}', [InstructorCurriculumController::class, 'updateLesson'])->name('courses.lessons.update');
        Route::delete('/courses/{course}/lessons/{lesson}', [InstructorCurriculumController::class, 'destroyLesson'])->name('courses.lessons.destroy');
        Route::post('/courses/{course}/lessons/{lesson}/quiz', [InstructorQuizController::class, 'store'])->name('courses.lessons.quiz.store');
        Route::post('/quizzes/{quiz}/questions', [InstructorQuizController::class, 'storeQuestion'])->name('quizzes.questions.store');
        Route::put('/quiz-questions/{question}', [InstructorQuizController::class, 'updateQuestion'])->name('quiz-questions.update');
        Route::delete('/quiz-questions/{question}', [InstructorQuizController::class, 'destroyQuestion'])->name('quiz-questions.destroy');
        Route::post('/quiz-questions/{question}/answers', [InstructorQuizController::class, 'storeAnswer'])->name('quiz-questions.answers.store');
        Route::put('/quiz-questions/{question}/answers', [InstructorQuizController::class, 'updateAnswers'])->name('quiz-questions.answers.update');
        Route::put('/quiz-answers/{answer}', [InstructorQuizController::class, 'updateAnswer'])->name('quiz-answers.update');
        Route::delete('/quiz-answers/{answer}', [InstructorQuizController::class, 'destroyAnswer'])->name('quiz-answers.destroy');
        Route::put('/courses/{course}', [InstructorCourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{course}', [InstructorCourseController::class, 'destroy'])->name('courses.destroy');
        Route::post('/courses/{course}/archive', [InstructorCourseController::class, 'archive'])->name('courses.archive');
        Route::post('/courses/{course}/chapters', [InstructorCourseController::class, 'addChapter'])->name('courses.chapters.store');
        Route::get('/courses/{course}/submit', [InstructorCourseController::class, 'submitPage'])->name('courses.submit.page');
        Route::post('/courses/{course}/submit', [InstructorCourseController::class, 'submit'])->name('courses.submit');
        Route::post('/chapters/{chapter}/lessons', [InstructorCourseController::class, 'addLesson'])->name('chapters.lessons.store');
    });
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
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::post('/categories/{category}/toggle-status', [AdminCategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
    Route::resource('coupons', AdminCouponController::class)->except(['show']);
    Route::post('coupons/{coupon}/toggle-status', [AdminCouponController::class, 'toggleStatus'])->name('coupons.toggle-status');
    Route::get('/courses', [ManageController::class, 'index'])->name('courses.index');
    Route::get('/course-reviews', [CourseReviewController::class, 'index'])->name('course-reviews.index');
    Route::get('/course-reviews/{course}', [CourseReviewController::class, 'show'])->name('course-reviews.show');
    Route::post('/course-reviews/{course}/approve', [CourseReviewController::class, 'approve'])->name('course-reviews.approve');
    Route::post('/course-reviews/{course}/reject', [CourseReviewController::class, 'reject'])->name('course-reviews.reject');
    Route::get('/student-reviews', [AdminStudentReviewController::class, 'index'])->name('student-reviews.index');
    Route::get('/student-reviews/{review}', [AdminStudentReviewController::class, 'show'])->name('student-reviews.show');
    Route::patch('/student-reviews/{review}/approve', [AdminStudentReviewController::class, 'approve'])->name('student-reviews.approve');
    Route::patch('/student-reviews/{review}/reject', [AdminStudentReviewController::class, 'reject'])->name('student-reviews.reject');
    Route::patch('/student-reviews/{review}/hide', [AdminStudentReviewController::class, 'hide'])->name('student-reviews.hide');
    Route::patch('/student-reviews/{review}/restore', [AdminStudentReviewController::class, 'restore'])->name('student-reviews.restore');
    Route::delete('/student-reviews/{review}', [AdminStudentReviewController::class, 'destroy'])->name('student-reviews.destroy');
    Route::get('/courses/pending', fn () => redirect()->route('admin.course-reviews.index'))->name('courses.pending');
    Route::get('/courses/{course}/review', [ManageController::class, 'review'])->name('courses.review');
    Route::get('/courses/{course}/students', [ManageController::class, 'students'])->name('courses.students');
    Route::post('/courses/{course}/approve', [ManageController::class, 'approve'])->name('courses.approve');
    Route::post('/courses/{course}/reject', [ManageController::class, 'reject'])->name('courses.reject');
    Route::post('/courses/{course}/review', [ManageController::class, 'submitReview'])->name('courses.submitReview');
    Route::post('/courses/{course}/publish', [ManageController::class, 'publish'])->name('courses.publish');
    Route::post('/ai-moderation/{lesson}/extract', [AiModerationController::class, 'extractFrames'])->name('ai-moderation.extract');
    Route::post('/ai-moderation/analyze-frame', [AiModerationController::class, 'analyzeFrame'])->name('ai-moderation.analyze-frame');
    Route::post('/ai-moderation/{lesson}/save', [AiModerationController::class, 'saveResults'])->name('ai-moderation.save');
    Route::get('/ai-moderation/{lesson}/stream-video', [AiModerationController::class, 'streamVideo'])->name('ai-moderation.stream-video');
    Route::post('/courses/{course}/archive', [ManageController::class, 'archive'])->name('courses.archive');
    Route::post('/courses/{course}/restore', [ManageController::class, 'restore'])->name('courses.restore');
    Route::get('/courses/{course}', [ManageController::class, 'show'])->name('courses.show');
    Route::get('/revenue', [ManageController::class, 'revenue'])->name('revenue');
    Route::get('/activity-logs', [ManageController::class, 'activityLogs'])->name('activity-logs');
    Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications', [AdminNotificationController::class, 'store'])->name('notifications.store');
    Route::get('/homepage', [ManageController::class, 'homepage'])->name('homepage');
    Route::put('/homepage', [ManageController::class, 'updateHomepage'])->name('homepage.update');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

if (app()->environment('local')) {
    Route::get('/dev/login-as-admin', function () {
        auth()->login(User::where('role', 'admin')->firstOrFail());

        return redirect()->route('admin.dashboard');
    })->name('dev.login-as-admin');

    Route::get('/dev/login-as-student', function () {
        $user = User::where('email', 'leanhtuan291111@gmail.com')->first()
            ?? User::where('role', 'student')->firstOrFail();

        auth()->login($user);

        return redirect()->route('dashboard');
    })->name('dev.login-as-student');
}
