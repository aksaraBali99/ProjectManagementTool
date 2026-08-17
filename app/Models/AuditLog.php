<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['organization_id', 'user_id', 'action', 'entity_type', 'entity_id', 'changes'])]
class AuditLog extends Model
{
    protected $table = 'audit_log';

    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'changes' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
