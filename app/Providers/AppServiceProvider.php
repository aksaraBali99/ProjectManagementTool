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

        // Founder Analytics: an owner-tier cross-company oversight view, not
        // an operational one — rides the same manage_settings Administration
        // bundle as the Audit Trail, rather than a dedicated permission.
        Gate::define('analytics.view', fn (User $user) => $user->hasPermission('manage_settings'));

        // Status & Priority Colors: a global (not org-scoped) admin
        // settings page — same manage_settings bundle as Analytics/Audit
        // Trail.
        Gate::define('task-colors.view', fn (User $user) => $user->hasPermission('manage_settings'));
    }
}
