<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use JsonException;

/**
 * task #124: the release notes, read from resources/release-notes.json
 * (config('solava.release_notes_path')): one entry per release, newest
 * first, added on main by its own PR before the release PR (see
 * docs/release-process.md). The file ships with the code it describes;
 * nothing is stored in the database and nothing is fetched from GitHub.
 *
 * Read and validated once per request (bound as a scoped instance in
 * AppServiceProvider). An invalid file never throws: releases() is empty
 * and error() says why, logged once, so only the Release notes page shows
 * it and no other page is affected.
 *
 * The GitHub jobs check the same file with jq (.github/scripts/
 * release-notes-check.sh); the rules here are the full set, and a test
 * runs them on the real file so a bad edit fails CI.
 */
class ReleaseNotes
{
    public const TYPES = ['Enhancement', 'Bugfix', 'Chore'];

    /** Types whose tasks must say what changed for people: before and after. */
    public const TYPES_NEEDING_BEFORE_AFTER = ['Enhancement', 'Bugfix'];

    /** @var list<array<string, mixed>>|null */
    private ?array $releases = null;

    private ?string $error = null;

    public function __construct(private readonly string $path) {}

    /**
     * Every release, newest first, as in the file. Empty when the file is
     * invalid (see error()).
     *
     * @return list<array{version: string, date: string, summary: string, tasks: list<array{number: int, type: string, title: string, before: ?string, after: ?string, prs: list<int>}>}>
     */
    public function releases(): array
    {
        $this->load();

        return $this->releases;
    }

    /** Why the file is invalid, or null when it's fine. */
    public function error(): ?string
    {
        $this->load();

        return $this->error;
    }

    private function load(): void
    {
        if ($this->releases !== null) {
            return;
        }

        try {
            $this->releases = $this->parse();
        } catch (InvalidReleaseNotes $e) {
            $this->releases = [];
            $this->error = $e->getMessage();
            Log::warning('The release notes file is invalid: '.$e->getMessage(), ['path' => $this->path]);
        }
    }

    /** @return list<array<string, mixed>> */
    private function parse(): array
    {
        if (! is_file($this->path)) {
            throw new InvalidReleaseNotes('the file '.basename($this->path).' is missing.');
        }

        try {
            $data = json_decode((string) file_get_contents($this->path), true, 64, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidReleaseNotes('it is not valid JSON ('.$e->getMessage().').');
        }

        if (! is_array($data) || ! array_key_exists('releases', $data) || ! is_array($data['releases']) || ! array_is_list($data['releases'])) {
            throw new InvalidReleaseNotes('it must be an object with a "releases" list.');
        }

        $releases = [];
        $previous = null;
        $seen = [];

        foreach ($data['releases'] as $index => $release) {
            $where = 'release #'.($index + 1);
            $release = $this->release($release, $where);

            if (isset($seen[$release['version']])) {
                throw new InvalidReleaseNotes("{$release['version']} appears more than once.");
            }
            $seen[$release['version']] = true;

            if ($previous !== null && self::compareVersions($release['version'], $previous) >= 0) {
                throw new InvalidReleaseNotes("the releases must be newest first, but {$release['version']} comes after {$previous}.");
            }
            $previous = $release['version'];

            $releases[] = $release;
        }

        return $releases;
    }

    /** @return array<string, mixed> */
    private function release(mixed $release, string $where): array
    {
        if (! is_array($release)) {
            throw new InvalidReleaseNotes("{$where} must be an object.");
        }

        $version = $release['version'] ?? null;
        if (! is_string($version) || ! self::isVersion($version)) {
            throw new InvalidReleaseNotes("{$where} needs a \"version\" like v2026.11.1.");
        }
        $where = $version;

        $date = $release['date'] ?? null;
        if (! is_string($date) || ! preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $m) || ! checkdate((int) $m[2], (int) $m[3], (int) $m[1])) {
            throw new InvalidReleaseNotes("{$where} needs a \"date\" like 2026-11-10.");
        }

        $summary = $this->text($release, 'summary', $where, required: true);

        $tasks = $release['tasks'] ?? null;
        if (! is_array($tasks) || ! array_is_list($tasks) || $tasks === []) {
            throw new InvalidReleaseNotes("{$where} needs a non-empty \"tasks\" list.");
        }

        $numbers = [];
        $parsedTasks = [];
        foreach ($tasks as $index => $task) {
            $task = $this->task($task, "{$where}, task entry #".($index + 1));
            if (isset($numbers[$task['number']])) {
                throw new InvalidReleaseNotes("{$where} lists task #{$task['number']} more than once.");
            }
            $numbers[$task['number']] = true;
            $parsedTasks[] = $task;
        }

        return ['version' => $version, 'date' => $date, 'summary' => $summary, 'tasks' => $parsedTasks];
    }

    /** @return array<string, mixed> */
    private function task(mixed $task, string $where): array
    {
        if (! is_array($task)) {
            throw new InvalidReleaseNotes("{$where} must be an object.");
        }

        $number = $task['number'] ?? null;
        if (! is_int($number) || $number < 1) {
            throw new InvalidReleaseNotes("{$where} needs a task \"number\" (a whole number, 1 or more).");
        }
        $where = preg_replace('/task entry #\d+$/', "task #{$number}", $where);

        $type = $task['type'] ?? null;
        if (! in_array($type, self::TYPES, true)) {
            throw new InvalidReleaseNotes("{$where} needs a \"type\": ".implode(', ', self::TYPES).'.');
        }

        $needsBeforeAfter = in_array($type, self::TYPES_NEEDING_BEFORE_AFTER, true);

        if (array_key_exists('pr', $task) && array_key_exists('prs', $task)) {
            throw new InvalidReleaseNotes("{$where} has both \"pr\" and \"prs\"; use one.");
        }
        $prs = array_key_exists('pr', $task) ? [$task['pr']] : ($task['prs'] ?? []);
        if (! is_array($prs) || ! array_is_list($prs)) {
            throw new InvalidReleaseNotes("{$where}: \"prs\" must be a list of PR numbers.");
        }
        foreach ($prs as $pr) {
            if (! is_int($pr) || $pr < 1) {
                throw new InvalidReleaseNotes("{$where}: a PR number must be a whole number, 1 or more.");
            }
        }

        return [
            'number' => $number,
            'type' => $type,
            'title' => $this->text($task, 'title', $where, required: true),
            'before' => $this->text($task, 'before', $where, required: $needsBeforeAfter),
            'after' => $this->text($task, 'after', $where, required: $needsBeforeAfter),
            'prs' => $prs,
        ];
    }

    /** A plain-text field: required ones must be non-empty. */
    private function text(array $item, string $field, string $where, bool $required): ?string
    {
        $value = $item[$field] ?? null;

        if ($value === null && ! $required) {
            return null;
        }

        if (! is_string($value) || trim($value) === '') {
            throw new InvalidReleaseNotes("{$where} needs \"{$field}\" (text).");
        }

        return $value;
    }

    /**
     * task #124 follow-up: a note's text as display blocks. Consecutive
     * lines starting "- " form one bullet list; other lines are
     * paragraphs, a blank line starting a new one, the line breaks inside
     * a paragraph kept. A "- " with nothing after it is skipped; "-x"
     * (no space) is an ordinary line. Only plain strings come out: the
     * view prints every piece with {{ }}, so nothing is ever HTML.
     *
     * @return list<array{type: 'p'|'ul', lines: list<string>}>
     */
    public static function blocks(?string $text): array
    {
        $blocks = [];
        $current = null;

        foreach (preg_split('/\R/', (string) $text) as $line) {
            $line = rtrim($line);
            $isBullet = str_starts_with($line, '- ') || $line === '-';
            $type = $isBullet ? 'ul' : 'p';

            if ($line === '') {
                $current = null;

                continue;
            }

            $content = $isBullet ? trim(substr($line, 2)) : $line;
            if ($isBullet && $content === '') {
                continue;
            }

            if ($current === null || $blocks[$current]['type'] !== $type) {
                $blocks[] = ['type' => $type, 'lines' => []];
                $current = array_key_last($blocks);
            }
            $blocks[$current]['lines'][] = $content;
        }

        return $blocks;
    }

    public static function isVersion(string $version): bool
    {
        return (bool) preg_match('/^v\d{4}\.\d{2}\.\d+$/', $version);
    }

    /** <0, 0 or >0, comparing year, month and N as numbers (v2026.11.10 > v2026.11.9). */
    public static function compareVersions(string $a, string $b): int
    {
        return array_map('intval', explode('.', ltrim($a, 'v'))) <=> array_map('intval', explode('.', ltrim($b, 'v')));
    }
}
