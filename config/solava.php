<?php

use App\Support\AppVersion;

return [

    /*
    |--------------------------------------------------------------------------
    | Per-request access profile cache
    |--------------------------------------------------------------------------
    |
    | task #72 phase 1. When true, each user's roles, permissions, company
    | memberships and department grants are loaded once per request and every
    | User access helper answers from memory. When false, every helper runs
    | its original fresh queries — identical answers, just slower.
    |
    | This is the rollback switch: set ACCESS_CACHE_ENABLED=false and run
    | `php artisan config:clear && php artisan config:cache` to fall back to
    | the old behaviour without deploying anything.
    |
    */

    'access_cache' => env('ACCESS_CACHE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Release notes
    |--------------------------------------------------------------------------
    |
    | task #124. The file Settings > Release notes reads: one entry per
    | release, newest first, added on main before each release PR (see
    | docs/release-process.md). Configurable so tests can point it at a
    | fixture.
    |
    */

    'release_notes_path' => resource_path('release-notes.json'),

    // The GitHub repository the release notes link to (each release's
    // GitHub Release, each task's PRs).
    'github_repo_url' => 'https://github.com/aksaraBali99/ProjectManagementTool',

    // SOL-0028: what the sidebar shows next to "Solava". Worked out when the
    // config is loaded, so config:cache (run by deploy.sh after every pull)
    // freezes it per deploy: no file reads per page on DEV and PROD.
    'build' => AppVersion::detect(resource_path('release-notes.json'), base_path('.git')),

];
