<?php

use App\Support\AppVersion;

/**
 * SOL-0028: the version and commit the sidebar shows, read from the release
 * notes file and from .git with plain file reads.
 */
beforeEach(function () {
    $this->dir = sys_get_temp_dir().'/app-version-'.bin2hex(random_bytes(6));
    mkdir($this->dir.'/.git/refs/heads', 0777, true);
    $this->notes = $this->dir.'/release-notes.json';
    $this->git = $this->dir.'/.git';
    $this->sha = 'a13a163558f8ec488564da4013fe4e88a6597176';
});

afterEach(function () {
    $remove = function (string $path) use (&$remove) {
        if (is_dir($path)) {
            foreach (scandir($path) as $entry) {
                if ($entry !== '.' && $entry !== '..') {
                    $remove($path.'/'.$entry);
                }
            }
            rmdir($path);
        } elseif (file_exists($path)) {
            unlink($path);
        }
    };
    $remove($this->dir);
});

function writeVersionNotes(string $path, mixed $releases): void
{
    file_put_contents($path, json_encode(['releases' => $releases]));
}

// --- The version ----------------------------------------------------------------

test('the newest entry\'s version is returned; older entries are ignored', function () {
    writeVersionNotes($this->notes, [['version' => 'v2026.11.2'], ['version' => 'v2026.11.1']]);

    expect(AppVersion::detect($this->notes, $this->git)['version'])->toBe('v2026.11.2');
});

test('no usable version gives null, without throwing', function (?string $contents) {
    if ($contents !== null) {
        file_put_contents($this->notes, $contents);
    }

    expect(AppVersion::detect($this->notes, $this->git)['version'])->toBeNull();
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
    $build = AppVersion::detect(dirname(__DIR__, 2).'/resources/release-notes.json', $this->git);

    expect($build['version'])->toMatch('/^v\d{4}\.\d{2}\.\d+$/');
});

// --- The commit -------------------------------------------------------------------

test('a branch HEAD with its loose ref file gives the 7-character commit', function () {
    file_put_contents($this->git.'/HEAD', "ref: refs/heads/main\n");
    file_put_contents($this->git.'/refs/heads/main', $this->sha."\n");

    expect(AppVersion::detect($this->notes, $this->git)['commit'])->toBe('a13a163');
});

test('a branch whose ref is only in packed-refs gives the commit', function () {
    file_put_contents($this->git.'/HEAD', "ref: refs/heads/production\n");
    file_put_contents($this->git.'/packed-refs', implode("\n", [
        '# pack-refs with: peeled fully-peeled sorted',
        '1111111111111111111111111111111111111111 refs/heads/main',
        $this->sha.' refs/heads/production',
        '^2222222222222222222222222222222222222222',
        '3333333333333333333333333333333333333333 refs/tags/v2026.11.1',
        '',
    ]));

    expect(AppVersion::detect($this->notes, $this->git)['commit'])->toBe('a13a163');
});

test('a detached HEAD gives its own commit', function () {
    file_put_contents($this->git.'/HEAD', $this->sha."\n");

    expect(AppVersion::detect($this->notes, $this->git)['commit'])->toBe('a13a163');
});

test('no usable commit gives null', function (string $case) {
    match ($case) {
        'no .git' => null,
        'garbage in HEAD' => file_put_contents($this->git.'/HEAD', "hello world\n"),
        'a short sha in HEAD' => file_put_contents($this->git.'/HEAD', "a13a163\n"),
        'a non-hex ref' => [file_put_contents($this->git.'/HEAD', "ref: refs/heads/main\n"), file_put_contents($this->git.'/refs/heads/main', str_repeat('z', 40)."\n")],
        'a ref in neither place' => file_put_contents($this->git.'/HEAD', "ref: refs/heads/gone\n"),
        'a ref outside .git' => file_put_contents($this->git.'/HEAD', "ref: refs/heads/../../../escape\n"),
    };
    $git = $case === 'no .git' ? $this->dir.'/missing/.git' : $this->git;

    expect(AppVersion::detect($this->notes, $git))->toBe(['version' => null, 'commit' => null]);
})->with(['no .git', 'garbage in HEAD', 'a short sha in HEAD', 'a non-hex ref', 'a ref in neither place', 'a ref outside .git']);
