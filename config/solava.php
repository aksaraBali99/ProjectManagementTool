<?php

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

];
