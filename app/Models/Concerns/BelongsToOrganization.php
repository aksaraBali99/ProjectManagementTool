<?php

namespace App\Models\Concerns;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait BelongsToOrganization
{
    public static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope('organization', function (Builder $builder) {
            if (! Auth::check()) {
                return;
            }

            $user = Auth::user();

            if ($user->isSuperAdmin() || $user->isOwner()) {
                return;
            }

            $builder->whereIn(
                $builder->getModel()->getTable().'.organization_id',
                $user->visibleOrganizationIds(),
            );
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
