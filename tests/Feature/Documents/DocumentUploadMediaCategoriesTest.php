<?php

use App\Models\Department;
use App\Models\Document;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * task #73 (document form fixes): a Document upload isn't limited to the
 * document category (PDF/Word/Excel/PowerPoint/text/CSV) alone anymore —
 * it can also be an image, audio, or video file, each validated against
 * ITS OWN category's size/type limits from config/filestorage.php (the
 * same limits the Rich Media work already established), not a single flat
 * limit for everything. Both the standalone Documents page and the Task
 * edit page's own inline upload go through the same shared
 * DocumentUploadService (see DocumentUploadFromLocalTest's own docblock),
 * so this is tested primarily against the Documents-page entry point
 * (POST /documents with no task_id), plus one test confirming the Task-
 * page entry point (task_id set) accepts the identical expanded set.
 */
beforeEach(function () {
    Storage::fake('r2');
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);
});

function fakeMediaFile(string $name, int $kilobytes, string $mime): UploadedFile
{
    return UploadedFile::fake()->create($name, $kilobytes, $mime);
}

test('an image uploads successfully as a Document, validated against the image category\'s own limit', function () {
    $file = fakeMediaFile('photo.jpg', 5 * 1024, 'image/jpeg'); // 5MB — under image's 10MB cap

    $response = $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->org->id,
        'name' => 'Photo',
        'file' => $file,
        'access_level' => 'internal',
    ]);

    $response->assertCreated();
    $document = Document::where('name', 'Photo')->firstOrFail();
    expect($document->mime_type)->toBe('image/jpeg');
    expect($document->storage_key)->toContain('/images/');
    Storage::disk('r2')->assertExists($document->storage_key);
});

test('an audio file uploads successfully as a Document, validated against the audio category\'s own limit', function () {
    $file = fakeMediaFile('voice-memo.mp3', 20 * 1024, 'audio/mpeg'); // 20MB — under audio's 50MB cap

    $response = $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->org->id,
        'name' => 'Voice memo',
        'file' => $file,
        'access_level' => 'internal',
    ]);

    $response->assertCreated();
    $document = Document::where('name', 'Voice memo')->firstOrFail();
    expect($document->mime_type)->toBe('audio/mpeg');
    expect($document->storage_key)->toContain('/audio/');
});

test('a video file uploads successfully as a Document, validated against the video category\'s own limit', function () {
    $file = fakeMediaFile('clip.mp4', 100 * 1024, 'video/mp4'); // 100MB — under video's 200MB cap

    $response = $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->org->id,
        'name' => 'Clip',
        'file' => $file,
        'access_level' => 'internal',
    ]);

    $response->assertCreated();
    $document = Document::where('name', 'Clip')->firstOrFail();
    expect($document->mime_type)->toBe('video/mp4');
    expect($document->storage_key)->toContain('/video/');
});

test('a file exceeding its OWN category\'s limit is rejected, even though it is under the document category\'s laxer limit', function () {
    // 11MB image — over image's 10MB cap, but well under document's 20MB
    // cap. Proves detection picks the IMAGE category (by mime/extension)
    // and validates against ITS limit, not document's laxer one — the
    // exact scenario the task's own spec calls out.
    $file = fakeMediaFile('big-photo.jpg', 11 * 1024, 'image/jpeg');

    $response = $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->org->id,
        'name' => 'Big photo',
        'file' => $file,
        'access_level' => 'internal',
    ]);

    $response->assertStatus(422);
    expect($response->json('message'))->toContain('Image files must be');
    expect(Document::where('name', 'Big photo')->exists())->toBeFalse();
    expect(Storage::disk('r2')->allFiles())->toBeEmpty();
});

test('a file type genuinely not covered by any category is rejected with a clear message', function () {
    $file = UploadedFile::fake()->create('archive.zip', 100, 'application/zip');

    $response = $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->org->id,
        'name' => 'Archive',
        'file' => $file,
        'access_level' => 'internal',
    ]);

    $response->assertStatus(422);
    expect($response->json('message'))->toContain('is not a supported file type');
    expect(Document::count())->toBe(0);
    expect(Storage::disk('r2')->allFiles())->toBeEmpty();
});

test('the document category (PDF etc.) still uploads successfully, unchanged, alongside the newly-accepted media categories', function () {
    $file = UploadedFile::fake()->create('report.pdf', 100, 'application/pdf');

    $response = $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->org->id,
        'name' => 'Report',
        'file' => $file,
        'access_level' => 'internal',
    ]);

    $response->assertCreated();
    expect(Document::where('name', 'Report')->firstOrFail()->storage_key)->toContain('/documents/');
});

test('a document category file still gets rejected for exceeding ITS OWN 20MB limit, not silently matched to a laxer category', function () {
    $file = UploadedFile::fake()->create('huge-report.pdf', 21 * 1024, 'application/pdf');

    $response = $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->org->id,
        'name' => 'Huge report',
        'file' => $file,
        'access_level' => 'internal',
    ]);

    $response->assertStatus(422);
    expect($response->json('message'))->toContain('Document files must be');
});

test('the Task edit page\'s own upload entry point accepts the identical expanded set, since it shares the same DocumentUploadService', function () {
    $task = Task::create(['organization_id' => $this->org->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);
    $file = fakeMediaFile('clip.mp4', 50 * 1024, 'video/mp4');

    $response = $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->org->id,
        'name' => 'Task video',
        'file' => $file,
        'access_level' => 'internal',
        'task_id' => $task->id,
    ]);

    $response->assertCreated();
    $document = Document::where('name', 'Task video')->firstOrFail();
    expect($document->mime_type)->toBe('video/mp4');
    expect($document->origin_task_id)->toBe($task->id);
    expect($document->storage_key)->toStartWith('tasks/'.$task->id.'/video/');
});
