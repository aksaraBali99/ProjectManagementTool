<?php

use App\Support\AppVersion;

/**
 * SOL-0028: the version the sidebar shows: the newest release notes entry's,
 * read with plain file reads.
 */
beforeEach(function () {
    $this->notes = sys_get_temp_dir().'/app-version-'.bin2hex(random_bytes(6)).'.json';
});

afterEach(function () {
    if (file_exists($this->notes)) {
        unlink($this->notes);
    }
});

test('the newest entry\'s version is returned; older entries are ignored', function () {
    file_put_contents($this->notes, json_encode(['releases' => [['version' => 'v2026.11.2'], ['version' => 'v2026.11.1']]]));

    expect(AppVersion::version($this->notes))->toBe('v2026.11.2');
});

test('no usable version gives null, without throwing', function (?string $contents) {
    if ($contents !== null) {
        file_put_contents($this->notes, $contents);
    }

    expect(AppVersion::version($this->notes))->toBeNull();
})->with([
    'a missing file' => [null],
    'invalid JSON' => ['{"releases": ['],
    'no releases list' => ['{"versions": []}'],
    'an empty releases list' => ['{"releases": []}'],
    'a version without the v' => ['{"releases": [{"version": "2026.11.2"}]}'],
    'a version without its number' => ['{"releases": [{"version": "v2026.11"}]}'],
    'a version that isn\'t text' => ['{"releases": [{"version": 2026}]}'],
    'a release that isn\'t an object' => ['{"releases": ["v2026.11.2"]}'],
    'a JSON string' => ['"v2026.11.2"'],
]);

test('the real resources/release-notes.json gives a release version', function () {
    expect(AppVersion::version(dirname(__DIR__, 2).'/resources/release-notes.json'))->toMatch('/^v\d{4}\.\d{2}\.\d+$/');
});
