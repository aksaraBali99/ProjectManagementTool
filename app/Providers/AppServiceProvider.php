<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // Not tied to a single Eloquent model — matrix screen spanning
        // organizations/departments/users — so a plain ability rather
        // than a model policy.
        Gate::define('access-control.view', fn (User $user) => $user->isSuperAdmin() || $user->isOwner());
    }
}
