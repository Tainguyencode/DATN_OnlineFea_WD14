<?php

namespace App\Providers;

use App\Models\Permission;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
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
        Event::listen(function (SocialiteWasCalled $event): void {
            $event->extendSocialite('microsoft', MicrosoftExtendSocialite::class);
        });

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
            if (! Auth::check() || ! Schema::hasTable('push_notifications')) {
                $view->with([
                    'unreadNotificationCount' => 0,
                    'recentNotifications' => collect(),
                ]);

                return;
            }

            $user = Auth::user();
            $notificationService = app(NotificationService::class);

            $view->with([
                'unreadNotificationCount' => $notificationService->unreadCount($user),
                'recentNotifications' => $user->pushNotifications()->latest()->limit(5)->get(),
            ]);
        });
    }
}
