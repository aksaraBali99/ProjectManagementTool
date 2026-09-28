<?php

use App\Models\Department;
use App\Models\Document;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('r2');
    $this->owner = createOwner();
    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);
});

test('a link-based document that still exists is not reported missing', function () {
    $document = Document::create([
        'organization_id' => $this->orgA->id,
        'uploaded_by' => $this->management->id,
        'name' => 'Doc',
        'link' => 'https://example.com/doc.pdf',
        'access_level' => 'internal',
    ]);

    $response = $this->actingAs($this->management)->postJson('/file-chip-status', [
        'hrefs' => [$document->link],
    ]);

    $response->assertOk();
    expect($response->json('missing'))->toBe([]);
});

test('an uploaded document resolved by its storage_key-derived URL is not reported missing', function () {
    $task = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);
    $url = $this->actingAs($this->management)->postJson("/tasks/{$task->id}/document-uploads", [
        'file' => UploadedFile::fake()->create('report.pdf', 100, 'application/pdf'),
        'context' => 'description',
    ])->assertCreated()->json('url');

    $response = $this->actingAs($this->management)->postJson('/file-chip-status', ['hrefs' => [$url]]);

    $response->assertOk();
    expect($response->json('missing'))->toBe([]);
});

test('a deleted document\'s href (link-based) is reported missing', function () {
    $document = Document::create([
        'organization_id' => $this->orgA->id,
        'uploaded_by' => $this->management->id,
        'name' => 'Doc',
        'link' => 'https://example.com/doc.pdf',
        'access_level' => 'internal',
    ]);
    $href = $document->link;
    $document->delete();

    $response = $this->actingAs($this->management)->postJson('/file-chip-status', ['hrefs' => [$href]]);

    $response->assertOk();
    expect($response->json('missing'))->toBe([$href]);
});

test('a deleted uploaded document\'s href (storage_key-based) is reported missing', function () {
    $task = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);
    $url = $this->actingAs($this->management)->postJson("/tasks/{$task->id}/document-uploads", [
        'file' => UploadedFile::fake()->create('report.pdf', 100, 'application/pdf'),
        'context' => 'description',
    ])->assertCreated()->json('url');
    $document = Document::where('link', $url)->firstOrFail();
    $document->delete();

    $response = $this->actingAs($this->management)->postJson('/file-chip-status', ['hrefs' => [$url]]);

    $response->assertOk();
    expect($response->json('missing'))->toBe([$url]);
});

test('a URL that never belonged to any document is reported missing, without leaking anything', function () {
    $response = $this->actingAs($this->management)->postJson('/file-chip-status', [
        'hrefs' => ['https://evil.example.com/whatever'],
    ]);

    $response->assertOk();
    expect($response->json('missing'))->toBe(['https://evil.example.com/whatever']);
});

test('the query count does not grow with the number of hrefs in the batch (two lookups, not one per href)', function () {
    // Warm-up: the first request in a test pays a one-time cost (session/
    // auth resolution) a later one on the same user doesn't — both the
    // baseline and scaled-up measurements below are taken after that's
    // already paid, so the comparison isolates what actually varies with
    // batch size.
    $this->actingAs($this->management)->postJson('/file-chip-status', ['hrefs' => ['https://example.com/warmup.pdf']])->assertOk();

    DB::enableQueryLog();
    $this->actingAs($this->management)->postJson('/file-chip-status', ['hrefs' => ['https://example.com/one.pdf']])->assertOk();
    $baselineQueries = count(DB::getQueryLog());
    DB::flushQueryLog();

    $documents = collect(range(1, 5))->map(fn (int $i) => Document::create([
        'organization_id' => $this->orgA->id,
        'uploaded_by' => $this->management->id,
        'name' => "Doc {$i}",
        'link' => "https://example.com/doc{$i}.pdf",
        'access_level' => 'internal',
    ]));
    $missingHrefs = collect(range(1, 5))->map(fn (int $i) => "https://example.com/missing{$i}.pdf");
    $hrefs = $documents->pluck('link')->merge($missingHrefs)->values()->all();

    // The query log stays enabled after flushQueryLog() (that only
    // clears entries, it doesn't turn logging off) — flush again here so
    // the fixture inserts above aren't counted in the measurement below.
    DB::flushQueryLog();

    DB::enableQueryLog();
    $response = $this->actingAs($this->management)->postJson('/file-chip-status', ['hrefs' => $hrefs]);
    $scaledUpQueries = count(DB::getQueryLog());
    DB::flushQueryLog();

    $response->assertOk();
    expect(collect($response->json('missing'))->sort()->values()->all())->toBe($missingHrefs->sort()->values()->all());
    expect($scaledUpQueries)->toBe($baselineQueries);
});

test('the batch is capped and an oversized request is rejected', function () {
    $tooMany = collect(range(1, 51))->map(fn (int $i) => "https://example.com/doc{$i}.pdf")->all();

    $this->actingAs($this->management)->postJson('/file-chip-status', ['hrefs' => $tooMany])->assertStatus(422);
});

test('the endpoint requires authentication', function () {
    $this->postJson('/file-chip-status', ['hrefs' => ['https://example.com/doc.pdf']])->assertUnauthorized();
});
