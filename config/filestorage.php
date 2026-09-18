<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Rich Media Storage
    |--------------------------------------------------------------------------
    |
    | Backs FileStorageService — every category (images, audio, video,
    | documents) lives in this one disk/bucket (single-vendor decision,
    | see the Rich Media Enhancement Guide), organized by key prefix per
    | category rather than split across disks.
    |
    */

    'disk' => env('FILE_STORAGE_DISK', 'r2'),

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    |
    | Per-category ceilings and allow-lists — env-backed so limits can be
    | tuned per environment without a code deploy, once real usage shows
    | whether these starting defaults are right. `prefix` is the folder
    | segment used under tasks/{task_id}/{prefix}/. `mime_types` and
    | `extensions` are both checked (defense in depth against a spoofed
    | Content-Type), so add to both when widening a category.
    |
    | The `document` category has no prior art to inherit from — the
    | existing Documents feature only stores external links today, never
    | an uploaded file — so this list is a reasonable starting default,
    | not a carried-over constraint.
    |
    */

    'categories' => [

        'image' => [
            'prefix' => 'images',
            'max_size' => env('FILE_STORAGE_IMAGE_MAX_KB', 10 * 1024) * 1024,
            'mime_types' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        ],

        'audio' => [
            'prefix' => 'audio',
            'max_size' => env('FILE_STORAGE_AUDIO_MAX_KB', 50 * 1024) * 1024,
            'mime_types' => ['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/x-wav', 'audio/wave', 'audio/mp4', 'audio/x-m4a'],
            'extensions' => ['mp3', 'wav', 'm4a'],
        ],

        'video' => [
            'prefix' => 'video',
            'max_size' => env('FILE_STORAGE_VIDEO_MAX_KB', 200 * 1024) * 1024,
            'mime_types' => ['video/mp4', 'video/webm', 'video/quicktime'],
            'extensions' => ['mp4', 'webm', 'mov'],
        ],

        'document' => [
            'prefix' => 'documents',
            'max_size' => env('FILE_STORAGE_DOCUMENT_MAX_KB', 20 * 1024) * 1024,
            'mime_types' => [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'text/plain',
                'text/csv',
            ],
            'extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv'],
        ],

    ],

];
