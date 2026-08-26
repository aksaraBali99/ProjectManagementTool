<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bulk Import Limits
    |--------------------------------------------------------------------------
    |
    | Row-count and upload-size ceilings for the Bulk Import feature.
    | Previously hardcoded class constants — moved here so they can be
    | tuned per environment (e.g. Hostinger shared hosting's PHP
    | max_execution_time) without a code deploy.
    |
    */

    // Total rows across every sheet in one uploaded file.
    'max_total_rows' => env('IMPORT_MAX_TOTAL_ROWS', 5000),

    // Uploaded file size, in kilobytes (Laravel's `max:` validation rule unit).
    'max_upload_kb' => env('IMPORT_MAX_UPLOAD_KB', 10240),

];
