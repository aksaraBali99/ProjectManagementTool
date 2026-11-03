<?php

// task #118: a worker process for TaskCodeTest's concurrency test. Boots
// the app against the SQLite file in DB_DATABASE and either sets up one
// company ("setup") or creates N tasks in it ("create N"), so several of
// these can create tasks at the same moment.
//
// Usage: php task-number-worker.php <app root> setup|create [count]

use App\Models\Department;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

[, $root, $action] = $argv;
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

// Writers queue for SQLite's single write lock instead of failing at once
// (MySQL's row lock waits the same way by default).
config(['database.connections.sqlite.busy_timeout' => 30000]);
DB::purge('sqlite');

if ($action === 'setup') {
    $organization = Organization::create(['name' => 'Concurrency Co', 'slug' => 'concurrency-co', 'code' => 'CCY', 'accent_color' => '#000000']);
    Department::create(['organization_id' => $organization->id, 'name' => 'Ops', 'color' => '#000000']);
    Project::create(['organization_id' => $organization->id, 'name' => 'Load', 'description' => 'd']);
    exit(0);
}

$organization = Organization::where('code', 'CCY')->firstOrFail();
$department = Department::withoutGlobalScopes()->where('organization_id', $organization->id)->firstOrFail();
$project = Project::withoutGlobalScopes()->where('organization_id', $organization->id)->firstOrFail();

for ($i = 0; $i < (int) ($argv[3] ?? 1); $i++) {
    Task::create([
        'organization_id' => $organization->id,
        'project_id' => $project->id,
        'department_id' => $department->id,
        'title' => 'Concurrent '.getmypid().' '.$i,
        'priority' => 'medium',
        'status' => 'pending',
    ]);
}
