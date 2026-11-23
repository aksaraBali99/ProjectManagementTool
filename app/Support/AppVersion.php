<?php

namespace App\Support;

/**
 * SOL-0028: the version the sidebar shows under "Solava": the newest entry
 * of resources/release-notes.json.
 *
 * On PROD that is the version the release gets: release-check makes a
 * release PR's or hotfix's entry the next version, and the deploy tags it.
 * If a deploy ever falls back to another tag (its run warns why), the menu
 * still shows the entry's version. The tag itself can't be read here:
 * GitHub creates it after the deploy, and the server only fetches the
 * branch.
 *
 * Called from config/solava.php, so config:cache (deploy.sh, after every
 * pull) freezes the answer per deploy. Plain PHP, no facades or helpers that
 * need the app, and it never throws: a missing file, invalid JSON or a
 * malformed version gives null.
 */
final class AppVersion
{
    private const VERSION_PATTERN = '/^v\d{4}\.\d{2}\.\d+$/';

    public static function version(string $releaseNotesPath): ?string
    {
        if (! is_file($releaseNotesPath) || ! is_readable($releaseNotesPath)) {
            return null;
        }
        $raw = @file_get_contents($releaseNotesPath);
        if ($raw === false) {
            return null;
        }

        $notes = json_decode($raw, true, 64);
        $version = is_array($notes) && is_array($notes['releases'] ?? null)
            ? ($notes['releases'][0]['version'] ?? null)
            : null;

        return is_string($version) && preg_match(self::VERSION_PATTERN, $version) ? $version : null;
    }
}
