<?php

namespace App\Services\Import;

/**
 * In-memory state threaded through the single validation pass —
 * everything a later sheet needs to know about an earlier sheet's rows
 * (does this Company/Department/Project/Task Ref exist, was it blocked by
 * an upstream failure) without re-querying import_rows or the database
 * repeatedly. Cross-sheet references are resolved by NAME here (not real
 * IDs) — validation only needs to confirm referential integrity, not
 * produce foreign keys; that happens at commit time against what's
 * actually been written by then.
 */
class ImportValidationContext
{
    /** @var array<string, true> normalized company name => valid (existing or new-in-file) */
    public array $validCompanyNames = [];

    /** @var array<string, true> normalized company name => blocked by its own row's failure */
    public array $blockedCompanyNames = [];

    /** @var array<string, true> normalized "companyName|departmentName" */
    public array $validDepartmentKeys = [];

    /** @var array<string, true> */
    public array $blockedDepartmentKeys = [];

    /** @var array<string, array{type: string, valid: bool, importRowId: int|null}> username => info, file-provided users only */
    public array $usersSeen = [];

    /** @var array<string, string> normalized email => username, file rows only (ambiguity detection) */
    public array $emailsSeenThisFile = [];

    /** @var array<string, array<string, string>> username => [normalized company name => role slug], merged file + existing DB roles */
    public array $companyRolesByUsername = [];

    /** @var array<string, true> normalized "username|companyName", dup-guard within the Company Roles sheet */
    public array $companyRoleFileKeys = [];

    /** @var array<string, true> normalized "companyName|projectName" */
    public array $validProjectKeys = [];

    /** @var array<string, true> */
    public array $blockedProjectKeys = [];

    /** @var array<int, array{title: string, companyName: string, projectName: string, blocked: bool}> Task Ref => info */
    public array $taskRefs = [];

    /** @var array<string, int> prefix => highest employee_id number claimed so far this pass */
    public array $employeeIdClaimed = [];

    /** @var array<string, array<string, string>> username => existing DB roles by normalized company name, memoized */
    public array $existingRolesCache = [];

    public function normalize(string $value): string
    {
        return mb_strtolower(trim($value));
    }

    public function companyKey(string $companyName): string
    {
        return $this->normalize($companyName);
    }

    public function departmentKey(string $companyName, string $departmentName): string
    {
        return $this->normalize($companyName).'|'.$this->normalize($departmentName);
    }

    public function projectKey(string $companyName, string $projectName): string
    {
        return $this->normalize($companyName).'|'.$this->normalize($projectName);
    }

    public function companyRoleFileKey(string $username, string $companyName): string
    {
        return $this->normalize($username).'|'.$this->normalize($companyName);
    }
}
