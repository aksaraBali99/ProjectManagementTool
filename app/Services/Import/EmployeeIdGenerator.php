<?php

namespace App\Services\Import;

use App\Models\User;

/**
 * Suggests the next sequential employee_id for a given user type — reused
 * both by the Import upload/commit flow (blank Employee ID cell) and the
 * in-app "Add User" form (suggested default value). Stateless: every call
 * re-derives the highest existing number from the users table, plus
 * whatever the caller has already claimed earlier in the same pass (a
 * single import file can mint several blank Employee IDs before any of
 * them are actually persisted, so relying on the DB alone would hand out
 * the same number twice).
 */
class EmployeeIdGenerator
{
    public const EMPLOYEE_PREFIX = 'EMP';

    public const CLIENT_PREFIX = 'CLIENT';

    /**
     * @param  array<string, int>  $alreadyClaimedThisPass  prefix => highest number already handed out this pass
     */
    public function next(string $type, array $alreadyClaimedThisPass = []): string
    {
        $prefix = $this->prefixFor($type);
        $highest = max($this->highestExistingNumber($prefix), $alreadyClaimedThisPass[$prefix] ?? 0);

        return ImportIdCodec::encode($prefix, $highest + 1);
    }

    public function prefixFor(string $type): string
    {
        return $type === 'client' ? self::CLIENT_PREFIX : self::EMPLOYEE_PREFIX;
    }

    private function highestExistingNumber(string $prefix): int
    {
        $highest = 0;

        User::query()
            ->where('employee_id', 'like', "{$prefix}-%")
            ->pluck('employee_id')
            ->each(function (string $employeeId) use ($prefix, &$highest) {
                $number = ImportIdCodec::decode($prefix, $employeeId);
                if ($number !== null) {
                    $highest = max($highest, $number);
                }
            });

        return $highest;
    }
}
