<?php

namespace App\Providers;

use App\Models\User;
use App\Support\Access\AccessProfileCache;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // task #72 phase 1: one access profile per user per request.
        // Scoped, not singleton — a scoped binding is reset between
        // requests under Octane and between queued jobs, which is exactly
        // the lifetime a profile may have.
        $this->app->scoped(AccessProfileCache::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // A queue worker is a long-lived process: without this, job 2
        // could answer from the roles job 1 saw. Queued jobs run outside
        // the HTTP middleware stack, so the request-level flush doesn't
        // cover them.
        Event::listen(JobProcessing::class, fn () => AccessProfileCache::flushAll());

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

        // Bulk Import: a global (not org-scoped) admin settings page — same
        // manage_settings bundle as Analytics/Audit Trail/Status & Priority
        // Colors.
        Gate::define('import.view', fn (User $user) => $user->hasPermission('manage_settings'));
    }
}
