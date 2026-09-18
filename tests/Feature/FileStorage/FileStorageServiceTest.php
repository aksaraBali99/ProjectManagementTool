<?php

/**
 * Runs entirely against Storage::fake('r2') — no live R2 credentials
 * exist in this environment yet. This validates FileStorageService's own
 * logic (validation, key layout, exception wrapping) correctly; it does
 * NOT prove real R2 credentials/endpoint/bucket are reachable. A live
 * smoke test (one real upload + url() + delete()) is still needed once
 * actual R2_* values are added to .env.
 */

use App\Enums\FileCategory;
use App\Exceptions\FileStorageException;
use App\Services\FileStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('r2');

    $this->service = new FileStorageService;
    $this->taskId = 42;
});

/**
 * A valid fake file for the given category — mime type set explicitly
 * (Illuminate's fake UploadedFile returns exactly what's passed here from
 * getMimeType(), it doesn't sniff content), so tests control validation
 * inputs precisely without depending on GD/Imagick being available.
 */
function fileFor(FileCategory $category, ?int $kilobytes = null): UploadedFile
{
    [$filename, $mime, $defaultKb] = match ($category) {
        FileCategory::Image => ['photo.jpg', 'image/jpeg', 100],
        FileCategory::Audio => ['clip.mp3', 'audio/mpeg', 500],
        FileCategory::Video => ['clip.mp4', 'video/mp4', 2000],
        FileCategory::Document => ['file.pdf', 'application/pdf', 200],
    };

    return UploadedFile::fake()->create($filename, $kilobytes ?? $defaultKb, $mime);
}

test('uploading a valid file of each category succeeds and returns a working url', function (FileCategory $category) {
    $stored = $this->service->upload(fileFor($category), $category, $this->taskId);

    expect($stored->path)->toStartWith("tasks/{$this->taskId}/{$category->prefix()}/")
        ->and($stored->url)->not->toBeEmpty();

    Storage::disk('r2')->assertExists($stored->path);
    expect($this->service->url($stored->path))->toBe($stored->url);
})->with(FileCategory::cases());

test('uploading a file exceeding the size limit for its category is rejected with a clear error', function (FileCategory $category) {
    $oversizeKb = intdiv($category->config()['max_size'], 1024) + 10;

    expect(fn () => $this->service->upload(fileFor($category, $oversizeKb), $category, $this->taskId))
        ->toThrow(FileStorageException::class, "{$category->label()} files must be");

    expect(Storage::disk('r2')->allFiles())->toBeEmpty();
})->with(FileCategory::cases());

test('uploading a disallowed file type is rejected with a clear error', function (FileCategory $category) {
    $file = UploadedFile::fake()->create('malware.exe', 10, 'application/x-msdownload');

    expect(fn () => $this->service->upload($file, $category, $this->taskId))
        ->toThrow(FileStorageException::class, 'not an allowed file type');

    expect(Storage::disk('r2')->allFiles())->toBeEmpty();
})->with(FileCategory::cases());

test('deleting a file removes it, and a subsequent retrieval attempt fails clearly', function () {
    $stored = $this->service->upload(fileFor(FileCategory::Image), FileCategory::Image, $this->taskId);

    $this->service->delete($stored->path);

    Storage::disk('r2')->assertMissing($stored->path);
    expect(fn () => $this->service->url($stored->path))
        ->toThrow(FileStorageException::class, 'No file exists');
});

test('retrieving a url for a file that was never uploaded fails clearly rather than returning a broken url', function () {
    expect(fn () => $this->service->url('tasks/999/images/does-not-exist.jpg'))
        ->toThrow(FileStorageException::class, 'No file exists');
});
