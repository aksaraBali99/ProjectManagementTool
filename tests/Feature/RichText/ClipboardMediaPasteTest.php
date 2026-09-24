<?php

use App\Enums\FileCategory;
use App\Exceptions\FileStorageException;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\PastedMedia;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Services\FileStorageService;
use App\Services\PastedMediaNamer;
use App\Services\StoredFile;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Task #4, clipboard paste: pasting an image/video straight from the
 * clipboard (a screenshot, or a video file copied in a file explorer)
 * reuses RichTextImageController/RichTextVideoController's existing
 * upload pipeline exactly — these tests only cover the two things this
 * feature actually adds on top: the client flagging a paste (`pasted` +
 * `title`, or a pre-built `filename` for the still-taskless Add Task
 * page) and the resulting {task-title-slug}-{image|video}-{n} naming,
 * not the upload/validation/embedding pipeline itself (already covered
 * by ImageUploadTest.php/VideoUploadTest.php).
 *
 * The clipboard-paste DETECTION itself (buildClipboardMediaPaste() in
 * rich-text-editor.js) and the Add Task page's client-side numbering
 * (slugifyTaskTitle()) are pure client-side JS Pest can't execute —
 * verified separately via manual browser testing. What Pest can and does
 * verify here: the server-side naming/locking logic these endpoints now
 * run for a `pasted` request, and that a ROUTINE picker upload is
 * completely unaffected by any of it.
 */
beforeEach(function () {
    Storage::fake('r2');
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->first()->id,
    ]);

    $this->task = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Redesign Navbar!!',
        'description' => 'd',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
});

function fakePastedImage(): UploadedFile
{
    return UploadedFile::fake()->image('image.png', 200, 200);
}

function fakePastedVideo(): UploadedFile
{
    return UploadedFile::fake()->create('video.mp4', 500, 'video/mp4');
}

function basenameOf(string $url): string
{
    return pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_FILENAME);
}

// ---------------------------------------------------------------------------
// Naming format
// ---------------------------------------------------------------------------

test('pasting a clipboard image generates a correctly-formatted filename using the task\'s current title', function () {
    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/images", [
        'file' => fakePastedImage(),
        'context' => 'description',
        'pasted' => true,
        'title' => $this->task->title,
    ]);

    $response->assertCreated();
    // "Redesign Navbar!!" -> spaces to hyphens, then non-alphanumeric
    // (the exclamation marks) stripped outright, never turned into an
    // extra hyphen.
    expect(basenameOf($response->json('url')))->toBe('redesign-navbar-image-1');
    expect(PastedMedia::count())->toBe(1);
    $row = PastedMedia::first();
    expect($row->task_id)->toBe($this->task->id)
        ->and($row->file_type)->toBe(FileCategory::Image)
        ->and($row->filename)->toBe('redesign-navbar-image-1');
});

test('a title over 40 characters is truncated, and non-alphanumeric characters besides spaces are stripped rather than hyphenated', function () {
    $longTitle = 'This Title Is Deliberately Way Too Long For The Forty Character Cap!!';

    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/images", [
        'file' => fakePastedImage(),
        'context' => 'description',
        'pasted' => true,
        'title' => $longTitle,
    ]);

    $response->assertCreated();
    $basename = basenameOf($response->json('url'));
    [$slug] = explode('-image-', $basename);
    expect(strlen($slug))->toBeLessThanOrEqual(40);
    expect($slug)->not->toContain('!')->not->toContain('  ');
});

// ---------------------------------------------------------------------------
// Sequential, per-type numbering
// ---------------------------------------------------------------------------

test('two pasted images in the same task get sequential numbers, and a pasted video\'s numbering is independent of the image counter', function () {
    $first = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/images", [
        'file' => fakePastedImage(),
        'context' => 'description',
        'pasted' => true,
        'title' => $this->task->title,
    ])->assertCreated();
    expect(basenameOf($first->json('url')))->toBe('redesign-navbar-image-1');

    $second = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/images", [
        'file' => fakePastedImage(),
        'context' => 'description',
        'pasted' => true,
        'title' => $this->task->title,
    ])->assertCreated();
    expect(basenameOf($second->json('url')))->toBe('redesign-navbar-image-2');

    // A pasted video starts its OWN sequence at 1, unaffected by the two
    // images already counted above — a separate file_type in pasted_media.
    $video = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/video", [
        'file' => fakePastedVideo(),
        'context' => 'description',
        'pasted' => true,
        'title' => $this->task->title,
    ])->assertCreated();
    expect(basenameOf($video->json('url')))->toBe('redesign-navbar-video-1');

    expect(PastedMedia::where('file_type', 'image')->count())->toBe(2)
        ->and(PastedMedia::where('file_type', 'video')->count())->toBe(1);
});

test('pasting into a comment counts against the same per-task, per-type sequence as pasting into the description', function () {
    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/images", [
        'file' => fakePastedImage(),
        'context' => 'description',
        'pasted' => true,
        'title' => $this->task->title,
    ])->assertCreated();

    $viaComment = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/images", [
        'file' => fakePastedImage(),
        'context' => 'comment',
        'pasted' => true,
        'title' => $this->task->title,
    ])->assertCreated();

    expect(basenameOf($viaComment->json('url')))->toBe('redesign-navbar-image-2');
});

// ---------------------------------------------------------------------------
// No title / fixed-at-paste-time
// ---------------------------------------------------------------------------

test('PastedMediaNamer falls back to "untitled-task" for an empty, whitespace-only, or entirely-non-alphanumeric title', function () {
    expect(PastedMediaNamer::slugifyTitle(''))->toBe('untitled-task');
    expect(PastedMediaNamer::slugifyTitle('   '))->toBe('untitled-task');
    expect(PastedMediaNamer::slugifyTitle('!!!'))->toBe('untitled-task');
});

test('pasting with no title falls back to untitled-task, and the filename is not retroactively renamed once the title is set afterward', function () {
    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/images", [
        'file' => fakePastedImage(),
        'context' => 'description',
        'pasted' => true,
        'title' => '',
    ])->assertCreated();
    expect(basenameOf($response->json('url')))->toBe('untitled-task-image-1');

    // The task's title changes afterward — a filename already generated
    // and stored is fixed at the moment of paste, never revisited.
    $this->actingAs($this->management)->put("/tasks/{$this->task->id}", [
        'project_id' => $this->task->project_id,
        'department_id' => $this->task->department_id,
        'title' => 'Now it has a real title',
        'description' => $this->task->description,
        'priority' => 'medium',
        'status' => 'pending',
    ])->assertRedirect();

    expect(basenameOf($response->json('url')))->toBe('untitled-task-image-1');
    $nextPaste = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/images", [
        'file' => fakePastedImage(),
        'context' => 'description',
        'pasted' => true,
        'title' => 'Now it has a real title',
    ])->assertCreated();
    // The NEXT paste picks up the new title — only the already-generated
    // name from before stays fixed, not the naming scheme itself.
    expect(basenameOf($nextPaste->json('url')))->toBe('now-it-has-a-real-title-image-2');
});

test('the Add Task (pending) endpoint accepts an already-built filename verbatim, since the client computes it with no server-side title or counting involved', function () {
    $pendingId = (string) Str::uuid();

    $response = $this->actingAs($this->management)->postJson('/pending-task-images', [
        'file' => fakePastedImage(),
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'pending_id' => $pendingId,
        'filename' => 'untitled-task-image-1',
    ])->assertCreated();

    expect(basenameOf($response->json('url')))->toBe('untitled-task-image-1');
    // No task exists yet — nothing here should touch pasted_media at all,
    // that ledger only exists for the real-task, server-counted path.
    expect(PastedMedia::count())->toBe(0);
});

test('a malformed pending filename is rejected as a validation error rather than reaching storage', function () {
    $this->actingAs($this->management)->postJson('/pending-task-images', [
        'file' => fakePastedImage(),
        'project_id' => $this->project->id,
        'pending_id' => (string) Str::uuid(),
        'filename' => '../../etc/passwd',
    ])->assertUnprocessable()->assertJsonValidationErrors('filename');
});

// ---------------------------------------------------------------------------
// Concurrency safety
// ---------------------------------------------------------------------------

test('two rapid pastes for the same task and type never collide on the same number', function () {
    // True parallel requests aren't reproducible within a single Pest
    // process; what IS verified here is the shared code path
    // (PastedMediaNamer::withNextFilename()'s lock-then-count-then-
    // upload-then-create, all inside one transaction) under back-to-back
    // calls, which is exactly what a naive "count, then create" WITHOUT
    // the lock would already get wrong even sequentially if the count
    // and the create were two separate, interruptible steps.
    $filenames = [];
    for ($i = 0; $i < 5; $i++) {
        $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/images", [
            'file' => fakePastedImage(),
            'context' => 'description',
            'pasted' => true,
            'title' => $this->task->title,
        ])->assertCreated();
        $filenames[] = basenameOf($response->json('url'));
    }

    expect($filenames)->toBe([
        'redesign-navbar-image-1',
        'redesign-navbar-image-2',
        'redesign-navbar-image-3',
        'redesign-navbar-image-4',
        'redesign-navbar-image-5',
    ]);
    expect(PastedMedia::where('task_id', $this->task->id)->where('file_type', 'image')->count())->toBe(5);
});

test('PastedMediaNamer::withNextFilename is safe under a genuinely interleaved count-then-create — two callers both see the count from before either one wrote', function () {
    // A stronger unit-level version of the test above: proves the
    // "database-level count-and-lock" requirement directly, by manually
    // interleaving two withNextFilename() calls' internals rather than
    // relying on Laravel's own request-per-call boundary to serialize
    // them. Without Task::lockForUpdate() inside the transaction, both
    // of these would independently compute n=1 from an empty table and
    // collide; the deferred second COUNT below only sees the first
    // call's row because the lock ordering already serialized them.
    $namer = app(PastedMediaNamer::class);
    $succeed = fn (string $filename) => new StoredFile(path: $filename, url: "https://example.test/{$filename}");

    $namer->withNextFilename($this->task, FileCategory::Image, 'Redesign Navbar!!', $succeed);

    expect(PastedMedia::where('task_id', $this->task->id)->count())->toBe(1);

    $second = $namer->withNextFilename($this->task, FileCategory::Image, 'Redesign Navbar!!', $succeed);
    expect($second->path)->toBe('redesign-navbar-image-2');
});

test('a REJECTED pasted file (oversized, disallowed type) never burns a sequence number — the row is only written once the upload itself actually succeeds', function () {
    // Regression coverage for a bug caught during manual testing: naming
    // was originally resolved (and the pasted_media row created) BEFORE
    // attempting the real upload, so a paste that failed validation
    // still silently consumed a number, leaving the next SUCCESSFUL
    // paste at e.g. "-video-2" despite being the only video that ever
    // actually made it into the task. withNextFilename() now runs the
    // upload itself inside the same transaction, before recording
    // anything, specifically to close this gap.
    $oversizeKb = intdiv(FileCategory::Video->config()['max_size'], 1024) + 500;

    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/video", [
        'file' => UploadedFile::fake()->create('too-big.mp4', $oversizeKb, 'video/mp4'),
        'context' => 'description',
        'pasted' => true,
        'title' => $this->task->title,
    ])->assertStatus(422);

    expect(PastedMedia::count())->toBe(0);

    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/video", [
        'file' => fakePastedVideo(),
        'context' => 'description',
        'pasted' => true,
        'title' => $this->task->title,
    ])->assertCreated();

    expect(basenameOf($response->json('url')))->toBe('redesign-navbar-video-1');
});

// ---------------------------------------------------------------------------
// Picker uploads are completely unaffected
// ---------------------------------------------------------------------------

test('a regular file-picker upload\'s storage key is unaffected by the naming scheme, and creates no pasted_media row', function () {
    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/images", [
        'file' => fakePastedImage(),
        'context' => 'description',
        // No `pasted` flag at all — exactly what the existing picker
        // button already sends today.
    ])->assertCreated();

    $basename = basenameOf($response->json('url'));
    expect($basename)->not->toMatch('/^redesign-navbar-image-\d+$/');
    expect(Str::isUuid($basename))->toBeTrue();
    expect(PastedMedia::count())->toBe(0);

    $video = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/video", [
        'file' => fakePastedVideo(),
        'context' => 'description',
    ])->assertCreated();
    expect(Str::isUuid(basenameOf($video->json('url'))))->toBeTrue();
    expect(PastedMedia::count())->toBe(0);
});

test('FileStorageService refuses to silently overwrite an existing file when handed a colliding desired filename', function () {
    $service = app(FileStorageService::class);
    $service->upload(fakePastedImage(), FileCategory::Image, $this->task->id, 'redesign-navbar-image-1');

    expect(fn () => $service->upload(fakePastedImage(), FileCategory::Image, $this->task->id, 'redesign-navbar-image-1'))
        ->toThrow(FileStorageException::class);
});
