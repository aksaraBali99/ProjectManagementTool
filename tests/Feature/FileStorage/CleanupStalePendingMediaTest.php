<?php

use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('r2');
});

test('deletes only pending files older than 24 hours, leaving recent ones and real task files alone', function () {
    Storage::disk('r2')->put('tasks/pending/old-1/images/a.jpg', 'x');
    Storage::disk('r2')->put('tasks/pending/old-2/images/b.jpg', 'x');
    Storage::disk('r2')->put('tasks/pending/fresh/images/c.jpg', 'x');
    Storage::disk('r2')->put('tasks/5/images/d.jpg', 'x'); // a real, already-reconciled file

    touchDiskFile('r2', 'tasks/pending/old-1/images/a.jpg', now()->subHours(30));
    touchDiskFile('r2', 'tasks/pending/old-2/images/b.jpg', now()->subDays(3));
    touchDiskFile('r2', 'tasks/pending/fresh/images/c.jpg', now()->subHours(2));
    touchDiskFile('r2', 'tasks/5/images/d.jpg', now()->subDays(10));

    $this->artisan('media:cleanup-stale-pending')->assertExitCode(0);

    Storage::disk('r2')->assertMissing('tasks/pending/old-1/images/a.jpg');
    Storage::disk('r2')->assertMissing('tasks/pending/old-2/images/b.jpg');
    Storage::disk('r2')->assertExists('tasks/pending/fresh/images/c.jpg');
    Storage::disk('r2')->assertExists('tasks/5/images/d.jpg');
});

test('sweeps a stale pending file regardless of its media category — audio the same as image', function () {
    // task #4 phase 4: confirms this command is genuinely category-agnostic
    // (a plain age check under tasks/pending/, no category-specific logic)
    // rather than assuming it because it happened to only ever see images.
    Storage::disk('r2')->put('tasks/pending/old/audio/a.wav', 'x');
    Storage::disk('r2')->put('tasks/pending/fresh/audio/b.wav', 'x');

    touchDiskFile('r2', 'tasks/pending/old/audio/a.wav', now()->subHours(30));
    touchDiskFile('r2', 'tasks/pending/fresh/audio/b.wav', now()->subHours(2));

    $this->artisan('media:cleanup-stale-pending')->assertExitCode(0);

    Storage::disk('r2')->assertMissing('tasks/pending/old/audio/a.wav');
    Storage::disk('r2')->assertExists('tasks/pending/fresh/audio/b.wav');
});

test('running with nothing pending is a no-op, not an error', function () {
    $this->artisan('media:cleanup-stale-pending')->assertExitCode(0);
});
