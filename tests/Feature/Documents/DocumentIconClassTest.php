<?php

use App\Enums\DocumentAccessLevel;
use App\Models\Document;
use App\Models\Organization;
use App\Models\User;

/**
 * task #73 (icon-by-type) + code-review follow-up: Document::iconClass()
 * picks a Tabler icon by mime_type prefix, but a null mime_type doesn't
 * always mean "external link" — TaskManagementController::attachDocumentChips()
 * can create a real upload record (a real storage_key) with a null
 * mime_type, if the pending file it reconciles from had already been
 * cleaned up by the time the task save ran. That row should still get the
 * generic file icon, not the link icon a genuine external link gets.
 */
beforeEach(function () {
    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->uploader = User::factory()->create();
});

function makeDocumentForIconTest(array $overrides = []): Document
{
    return Document::create(array_merge([
        'organization_id' => test()->org->id,
        'uploaded_by' => test()->uploader->id,
        'name' => 'Doc',
        'access_level' => DocumentAccessLevel::Internal,
    ], $overrides));
}

test('an image mime_type gets the photo icon', function () {
    $document = makeDocumentForIconTest(['storage_key' => 'tasks/1/images/a.jpg', 'mime_type' => 'image/jpeg']);
    expect($document->iconClass)->toBe('ti-photo');
});

test('an audio mime_type gets the music icon', function () {
    $document = makeDocumentForIconTest(['storage_key' => 'tasks/1/audio/a.mp3', 'mime_type' => 'audio/mpeg']);
    expect($document->iconClass)->toBe('ti-music');
});

test('a video mime_type gets the video icon', function () {
    $document = makeDocumentForIconTest(['storage_key' => 'tasks/1/video/a.mp4', 'mime_type' => 'video/mp4']);
    expect($document->iconClass)->toBe('ti-video');
});

test('a document (or any other) mime_type gets the generic file icon', function () {
    $document = makeDocumentForIconTest(['storage_key' => 'tasks/1/documents/a.pdf', 'mime_type' => 'application/pdf']);
    expect($document->iconClass)->toBe('ti-file-text');
});

test('a genuine external link (no storage_key, no mime_type) gets the link icon', function () {
    $document = makeDocumentForIconTest(['link' => 'https://example.com/a.pdf']);
    expect($document->storage_key)->toBeNull();
    expect($document->iconClass)->toBe('ti-link');
});

test('a real upload with a missing mime_type (e.g. its pending file was already cleaned up when a file-chip was reconciled) gets the generic file icon, not the misleading link icon', function () {
    // Mirrors TaskManagementController::attachDocumentChips()'s own
    // $exists === false branch: link + storage_key are both set (it IS a
    // real upload reference), but mime_type is null.
    $document = makeDocumentForIconTest([
        'link' => 'https://cdn.example.com/tasks/1/documents/a.pdf',
        'storage_key' => 'tasks/1/documents/a.pdf',
        'mime_type' => null,
    ]);

    expect($document->iconClass)->toBe('ti-file-text');
});
