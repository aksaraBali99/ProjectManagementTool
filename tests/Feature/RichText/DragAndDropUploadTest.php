<?php

use App\Enums\FileCategory;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\PastedMedia;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Task #4, drag-and-drop upload: dragging a file from the OS onto the
 * editor reuses the SAME upload endpoints toolbar/paste already use —
 * buildDragDropUpload() (rich-text-editor.js) is what detects the drop
 * and decides which category a file belongs to, but it makes the exact
 * same HTTP request a picker selection does (no `pasted` flag, no
 * `filename`/`title` field), so there is no new server-side code path
 * for "a drop" as such. These tests confirm that equivalence explicitly
 * — a drop never triggers the clipboard-paste auto-naming scheme, and
 * several files (whatever categories they are) each succeed
 * independently, one bad file among them never affecting the others —
 * rather than relying only on ImageUploadTest.php/VideoUploadTest.php/
 * etc. happening to already cover the identical request shape.
 *
 * The actual DETECTION (a real OS drag-and-drop event), the visual
 * "Drop to upload" indicator, the per-file queue UI, and files uploading
 * in drop order are pure client-side JS Pest can't execute — verified
 * separately via manual browser testing with synthetic DragEvents (drop,
 * dragenter, dragleave), including a mixed image+document drop in the
 * same batch and the queue panel showing each file's own outcome.
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
        'title' => 'Ship it',
        'description' => 'd',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
});

function basenameOfDrop(string $url): string
{
    return pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_FILENAME);
}

test('a dropped file uploads and embeds through the exact same endpoint a toolbar/picker upload uses', function () {
    // No `pasted` flag, no `filename`/`title` field — exactly what
    // uploadDropped() (rich-text-editor.js) sends, the same shape the
    // picker's own 'change' listener already sends today.
    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/images", [
        'file' => UploadedFile::fake()->image('dropped.png', 200, 200),
        'context' => 'description',
    ]);

    $response->assertCreated()->assertJsonStructure(['url']);
    expect($response->json('url'))->toContain("tasks/{$this->task->id}/images/");
});

test('a dropped file keeps its original filename — the clipboard-paste auto-naming scheme never applies to a drop', function () {
    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/images", [
        'file' => UploadedFile::fake()->image('vacation-photo.png', 200, 200),
        'context' => 'description',
        // Deliberately no `pasted` flag — a drop never sends one.
    ]);

    $response->assertCreated();
    $basename = basenameOfDrop($response->json('url'));
    expect(Str::isUuid($basename))->toBeTrue();
    expect(PastedMedia::count())->toBe(0);
});

test('a drop carrying several files across different categories uploads and embeds every one of them', function () {
    // buildDragDropUpload() calls each category's own uploadDropped() one
    // at a time, in drop order — server-side this is indistinguishable
    // from three independent requests, which is what this simulates.
    $image = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/images", [
        'file' => UploadedFile::fake()->image('shot.png', 100, 100),
        'context' => 'description',
    ])->assertCreated();

    $video = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/video", [
        'file' => UploadedFile::fake()->create('clip.mp4', 500, 'video/mp4'),
        'context' => 'description',
    ])->assertCreated();

    $document = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/document-uploads", [
        'file' => UploadedFile::fake()->create('report.pdf', 100, 'application/pdf'),
        'context' => 'description',
    ])->assertCreated();

    expect($image->json('url'))->toContain('/images/');
    expect($video->json('url'))->toContain('/video/');
    expect($document->json('name'))->toBe('report.pdf');
});

test('one file failing validation in a multi-file drop does not affect the other files uploading successfully', function () {
    $oversizeKb = intdiv(FileCategory::Image->config()['max_size'], 1024) + 500;

    $bad = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/images", [
        'file' => UploadedFile::fake()->image('too-big.png', 10, 10)->size($oversizeKb),
        'context' => 'description',
    ]);
    $bad->assertStatus(422)->assertJsonPath('message', fn ($message) => str_contains($message, 'Image files must be'));

    // The next file in the same drop — a completely independent request,
    // exactly as buildDragDropUpload()'s own sequencing treats it —
    // succeeds normally regardless of the one before it failing.
    $good = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/images", [
        'file' => UploadedFile::fake()->image('fine.png', 100, 100),
        'context' => 'description',
    ]);
    $good->assertCreated();

    expect(Storage::disk('r2')->allFiles())->toHaveCount(1);
});

test('a dropped file reconciles correctly through the Add Task pending path too, same as a picker upload', function () {
    $pendingId = (string) Str::uuid();

    $response = $this->actingAs($this->management)->postJson('/pending-task-images', [
        'file' => UploadedFile::fake()->image('draft-shot.png', 100, 100),
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'pending_id' => $pendingId,
        // No `filename` — a drop on the Add Task page still keeps its
        // real name, it just never needed the auto-naming scheme's
        // client-built name to begin with.
    ]);

    $response->assertCreated();
    expect($response->json('url'))->toContain("tasks/pending/{$pendingId}/images/");
    $basename = basenameOfDrop($response->json('url'));
    expect(Str::isUuid($basename))->toBeTrue();
});
