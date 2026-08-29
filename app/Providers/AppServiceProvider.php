<?php

namespace App\Providers;

use App\Listeners\AwardLoginPoints;
use App\Models\Permission;
use App\Models\Wishlist;
use App\Services\NotificationService;
use Illuminate\Auth\Events\Login;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Microsoft\MicrosoftExtendSocialite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->assertSafeTestingDatabase();

        RateLimiter::for('checkout-payment', function (Request $request): Limit {
            $userKey = $request->user()
                ? 'user:'.$request->user()->getAuthIdentifier()
                : 'ip:'.$request->ip();
            $orderKey = (string) $request->route('order_code', 'unknown');

            return Limit::perMinute(10)->by('checkout:'.$userKey.':order:'.$orderKey);
        });

        Event::listen(function (SocialiteWasCalled $event): void {
            $event->extendSocialite('microsoft', MicrosoftExtendSocialite::class);
        });

        Event::listen(
            Login::class,
            AwardLoginPoints::class
        );

        Gate::before(fn ($user) => $user->isAdmin() ? true : null);

        try {
            if (Schema::hasTable('permissions')) {
                foreach (Permission::pluck('slug') as $permission) {
                    Gate::define($permission, fn ($user) => $user->hasPermissionTo($permission));
                }
            }
        } catch (\Throwable) {
            // Test and fresh CLI contexts may not have a database driver ready yet.
        }

        View::composer(['layouts.app', 'components.layouts.dashboard', 'components.notifications.bell'], function ($view): void {
            if (! Auth::check()) {
                $view->with([
                    'unreadNotificationCount' => 0,
                    'recentNotifications' => collect(),
                    'unreadStudyGroupCount' => 0,
                    'favoriteCourseCount' => 0,
                ]);

                return;
            }

            $user = Auth::user();
            $favoriteCourseCount = 0;
            $isHeaderLayout = in_array($view->getName(), ['layouts.app', 'components.layouts.dashboard'], true);
            if ($isHeaderLayout && $user->isStudent() && Schema::hasTable('wishlists') && Schema::hasTable('courses')) {
                $favoriteCourseCount = Wishlist::query()
                    ->where('user_id', $user->id)
                    ->whereHas('course', fn ($query) => $query->published())
                    ->select('course_id')
                    ->distinct()
                    ->count();
            }

            if (! Schema::hasTable('push_notifications')) {
                $view->with([
                    'unreadNotificationCount' => 0,
                    'recentNotifications' => collect(),
                    'unreadStudyGroupCount' => 0,
                    'favoriteCourseCount' => $favoriteCourseCount,
                ]);

                return;
            }

            $notificationService = app(NotificationService::class);

            $view->with([
                'unreadNotificationCount' => $notificationService->unreadCount($user),
                'recentNotifications' => $user->pushNotifications()->latest()->limit(5)->get(),
                'unreadStudyGroupCount' => $user->pushNotifications()->where('is_read', false)->where('type', 'study_group')->count(),
                'favoriteCourseCount' => $favoriteCourseCount,
            ]);
        });
    }

    /**
     * PHPUnit and testing Artisan commands may reset their database through
     * RefreshDatabase or migrate:fresh. Refuse to boot in testing when the
     * resolved connection is anything other than the dedicated test schema.
     */
    private function assertSafeTestingDatabase(): void
    {
        if (! $this->app->environment('testing')) {
            return;
        }

        $connection = (string) config('database.default');
        $database = (string) config("database.connections.{$connection}.database");

        if ($database !== 'web_onlinefea_test') {
            throw new \LogicException(
                "Unsafe testing database [{$database}]. Automated tests may only use [web_onlinefea_test].",
            );
        }
    }
}
