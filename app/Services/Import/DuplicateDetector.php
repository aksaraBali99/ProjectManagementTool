<?php

namespace App\Services\Import;

/**
 * Soft-warning (never blocking) near-duplicate detection — flags likely
 * duplicate Users (similar name/email) and near-duplicate Task titles.
 * Scoped to same-company (users) / same-project (task titles) candidates
 * only, so this stays cheap even on a large existing dataset.
 */
class DuplicateDetector
{
    private const SIMILARITY_THRESHOLD = 85.0;

    /**
     * @param  array<int, array{name: string, email: string}>  $candidates  existing users in the same company
     */
    public function findSimilarUser(string $name, string $email, array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if ($this->isSimilar($name, $candidate['name']) || $this->isSimilar($email, $candidate['email'])) {
                return "Looks similar to an existing user ({$candidate['name']}, {$candidate['email']}) — double-check this isn't a duplicate.";
            }
        }

        return null;
    }

    /**
     * @param  array<int, string>  $existingTitles  existing task titles in the same project
     */
    public function findSimilarTaskTitle(string $title, array $existingTitles): ?string
    {
        foreach ($existingTitles as $existingTitle) {
            if ($this->isSimilar($title, $existingTitle)) {
                return "Looks similar to an existing task in this project (\"{$existingTitle}\") — double-check this isn't a duplicate.";
            }
        }

        return null;
    }

    /**
     * Callers only ever reach this once the row has already failed to
     * match an existing record by its real identity field(s) (username/
     * email for a User, (project, title) for a Task) — so an exact match
     * here, e.g. two different people sharing one name, is exactly the
     * kind of thing worth flagging, not a case to special-case away.
     */
    private function isSimilar(string $a, string $b): bool
    {
        $a = mb_strtolower(trim($a));
        $b = mb_strtolower(trim($b));

        if ($a === '' || $b === '') {
            return false;
        }

        if ($a === $b) {
            return true;
        }

        similar_text($a, $b, $percent);

        return $percent >= self::SIMILARITY_THRESHOLD;
    }
}
