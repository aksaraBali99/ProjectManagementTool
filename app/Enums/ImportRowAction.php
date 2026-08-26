<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumValues;

enum ImportRowAction: string
{
    use HasEnumValues;

    case Insert = 'insert';
    case Update = 'update';
    case NoChange = 'no_change';
    case Blocked = 'blocked';

    /**
     * Company Roles rows are never insert/update-tracked like every other
     * sheet — every import fully re-synchronizes (username, company) role
     * pairs present in the file, so a valid Company Roles row always
     * resolves to this action rather than Insert/Update/NoChange.
     */
    case Sync = 'sync';

    public function label(): string
    {
        return match ($this) {
            self::Insert => 'Insert',
            self::Update => 'Update',
            self::NoChange => 'No Change',
            self::Blocked => 'Blocked',
            self::Sync => 'Sync',
        };
    }
}
