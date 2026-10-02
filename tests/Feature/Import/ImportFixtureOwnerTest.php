<?php

use App\Services\Import\DuplicateDetector;

/**
 * Guards the invariant createImportOwner() exists for: the Import tests'
 * own owner fixture must never look like a duplicate of any user the
 * import fixtures create, or it silently turns `valid` rows into
 * `warning` ones and fails tests that have nothing to do with duplicate
 * detection.
 *
 * This used to be Faker-random (createOwner()), and safeEmail() draws
 * from the same @example.com/.net/.org domains the fixtures use — a short
 * random local part makes the shared domain dominate similar_text
 * ("tvon@example.com" vs "one@example.com" = 90.3%), tripping the 85%
 * threshold about 0.46% of runs. That is exactly the kind of failure
 * that's invisible locally and reappears at random in CI, so it's worth a
 * test rather than a comment.
 *
 * Deliberately exercises the real DuplicateDetector rather than
 * re-implementing its threshold here — if that rule is ever retuned, this
 * follows it instead of asserting against a stale copy of it.
 */
test('the import owner fixture is not similar to any import fixture user', function () {
    $owner = createImportOwner();
    $candidates = [['name' => $owner->name, 'email' => $owner->email]];

    $detector = new DuplicateDetector;

    // Every (name, email) pair the Import test fixtures actually use.
    $fixtureUsers = [
        ['User One', 'one@example.com'],
        ['User Two', 'two@example.com'],
        ['Alice B', 'alice@example.com'],
        ['Bob', 'bob@example.com'],
        ['Jane Doe', 'jane.doe@example.com'],
        ['Jane Doe', 'jane@example.com'],
        ['Acme Contact', 'contact@acme.com'],
    ];

    foreach ($fixtureUsers as [$name, $email]) {
        expect($detector->findSimilarUser($name, $email, $candidates))
            ->toBeNull("Import owner fixture looks like a duplicate of {$name} <{$email}>");
    }
});

test('the import owner fixture is deterministic, not Faker-random', function () {
    // The whole point: two runs must produce the same identity, so the
    // duplicate-detection outcome above can't vary between runs.
    $owner = createImportOwner();

    expect($owner->name)->toBe('Import Fixture Owner')
        ->and($owner->email)->toBe('import.fixture.owner@fixtures.test');
});
