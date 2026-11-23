<?php

namespace App\Support;

/**
 * SOL-0028: the version the sidebar shows next to "Solava".
 *
 * - version: the newest entry of resources/release-notes.json. On PROD that
 *   is exactly the tag the deploy creates (release-check makes the release
 *   PR's or hotfix's entry the version it gets). The tag itself can't be
 *   read here: GitHub creates it after the deploy, and the server only
 *   fetches the branch.
 * - commit: the checked-out commit, read from .git with plain file reads,
 *   never by running git (shared hosting may block it, and it would cost a
 *   process per page).
 *
 * Called from config/solava.php, so config:cache (deploy.sh, after every
 * pull) freezes the answer per deploy. Plain PHP, no facades or helpers that
 * need the app, and it never throws: anything missing or malformed gives
 * null for that part.
 */
final class AppVersion
{
    private const VERSION_PATTERN = '/^v\d{4}\.\d{2}\.\d+$/';

    private const SHA_PATTERN = '/^[0-9a-f]{40}$/';

    /** @return array{version: ?string, commit: ?string} */
    public static function detect(string $releaseNotesPath, string $gitDir): array
    {
        return [
            'version' => self::version($releaseNotesPath),
            'commit' => self::commit($gitDir),
        ];
    }

    private static function version(string $path): ?string
    {
        $raw = self::read($path);
        if ($raw === null) {
            return null;
        }

        $notes = json_decode($raw, true, 64);
        $version = is_array($notes) && is_array($notes['releases'] ?? null)
            ? ($notes['releases'][0]['version'] ?? null)
            : null;

        return is_string($version) && preg_match(self::VERSION_PATTERN, $version) ? $version : null;
    }

    private static function commit(string $gitDir): ?string
    {
        $head = self::read($gitDir.'/HEAD');
        if ($head === null) {
            return null;
        }
        $head = trim($head);

        if (preg_match('#^ref: (refs/heads/[^\s]+)$#', $head, $match)) {
            $sha = self::looseRef($gitDir, $match[1]) ?? self::packedRef($gitDir, $match[1]);
        } else {
            // Detached HEAD: the sha itself.
            $sha = $head;
        }

        return $sha !== null && preg_match(self::SHA_PATTERN, $sha) ? substr($sha, 0, 7) : null;
    }

    private static function looseRef(string $gitDir, string $ref): ?string
    {
        // A branch name never contains "..": don't read outside .git.
        if (str_contains($ref, '..')) {
            return null;
        }
        $sha = self::read($gitDir.'/'.$ref);

        return $sha === null ? null : trim($sha);
    }

    private static function packedRef(string $gitDir, string $ref): ?string
    {
        $packed = self::read($gitDir.'/packed-refs');
        if ($packed === null) {
            return null;
        }

        foreach (preg_split('/\R/', $packed) as $line) {
            if ($line === '' || $line[0] === '#' || $line[0] === '^') {
                continue;
            }
            $parts = explode(' ', trim($line), 2);
            if (count($parts) === 2 && $parts[1] === $ref) {
                return $parts[0];
            }
        }

        return null;
    }

    /** A file's contents, or null when it isn't a readable file. */
    private static function read(string $path): ?string
    {
        if (! is_file($path) || ! is_readable($path)) {
            return null;
        }
        $contents = @file_get_contents($path);

        return $contents === false ? null : $contents;
    }
}
