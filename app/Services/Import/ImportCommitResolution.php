<?php

namespace App\Services\Import;

/**
 * Mirrors ImportValidationContext's shape but holds real database ids,
 * built incrementally as each commit stage writes — every later stage
 * resolves foreign keys against this map, never against
 * import_rows.raw_data text, so a Task's Project Name correctly resolves
 * to a Project inserted moments earlier in the SAME commit.
 */
class ImportCommitResolution
{
    /** @var array<string, int> normalized company name => organization_id */
    public array $organizationIdsByName = [];

    /** @var array<string, int> normalized "companyName|departmentName" => department_id */
    public array $departmentIdsByKey = [];

    /** @var array<string, int> username => user_id */
    public array $userIdsByUsername = [];

    /** @var array<string, int> normalized "companyName|projectName" => project_id */
    public array $projectIdsByKey = [];

    /** @var array<int, int> Task Ref => task_id */
    public array $taskIdsByRef = [];

    /**
     * Memoizes ImportCommitService's DB-fallback resolver lookups (a
     * reference to something pre-existing, not newly written in this same
     * commit) — a name mentioned many times across a large file would
     * otherwise re-query on every mention.
     *
     * @var array<string, int|null>
     */
    public array $fallbackIdCache = [];

    public function normalize(string $value): string
    {
        return mb_strtolower(trim($value));
    }

    public function departmentKey(string $companyName, string $departmentName): string
    {
        return $this->normalize($companyName).'|'.$this->normalize($departmentName);
    }

    public function projectKey(string $companyName, string $projectName): string
    {
        return $this->normalize($companyName).'|'.$this->normalize($projectName);
    }
}
