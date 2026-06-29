<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
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
                foreach (\App\Models\Permission::pluck('slug') as $permission) {
                    Gate::define($permission, fn ($user) => $user->hasPermissionTo($permission));
                }
            }
        } catch (\Throwable) {
            // Test and fresh CLI contexts may not have a database driver ready yet.
        }
    }
}
