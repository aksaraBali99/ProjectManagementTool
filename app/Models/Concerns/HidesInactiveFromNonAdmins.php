<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HidesInactiveFromNonAdmins
{
    public static function bootHidesInactiveFromNonAdmins(): void
    {
        static::addGlobalScope('active', function (Builder $builder) {
            if (! Auth::check()) {
                return;
            }

            $user = Auth::user();

            if ($user->hasGlobalRole()) {
                return;
            }

            $builder->where($builder->getModel()->getTable().'.is_active', true);
        });
    }
}
