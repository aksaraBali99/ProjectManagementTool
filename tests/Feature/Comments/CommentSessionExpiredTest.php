<?php

use App\Models\Comment;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Hash;

/**
 * SOL-0022 subtask 4: the server side the "session expired" handling in
 * tasks/_comments.blade.php (session-expiry.js) relies on. A signed-out
 * JSON request to the comment endpoints gets a 401 JSON answer, never a
 * redirect, so the page can tell; and opening the task signed out sends
 * the user to sign in and back to the task, where the saved comment is
 * sent. The browser part is checked by hand (see the PR).
 *
 * The other answer the page treats as "expired" is 419 (a stale CSRF
 * token). Tests normally skip the CSRF check, so the 419 test turns it
 * back on.
 */
beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    $this->user = User::factory()->create(['username' => 'mona', 'password' => Hash::make('correct-password')]);
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->user->id,
        'role_id' => Role::where('slug', 'management')->first()->id,
    ]);

    $this->task = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Task',
        'description' => 'd',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $this->comment = Comment::create(['task_id' => $this->task->id, 'user_id' => $this->user->id, 'body' => '<p>Hi</p>']);
});

test('signed out, every comment endpoint answers 401 JSON, not a redirect', function (string $method, string $url, array $data) {
    $url = str_replace(['{task}', '{comment}'], [$this->task->id, $this->comment->id], $url);

    $this->json($method, $url, $data)
        ->assertUnauthorized()
        ->assertExactJson(['message' => 'Unauthenticated.']);

    expect(Comment::count())->toBe(1);
    expect($this->comment->fresh()->body)->toBe('<p>Hi</p>');
})->with([
    'post a comment' => ['POST', '/tasks/{task}/comments', ['body' => '<p>New</p>']],
    'reply' => ['POST', '/tasks/{task}/comments', ['body' => '<p>Reply</p>', 'parent_comment_id' => 0]],
    'edit' => ['PUT', '/comments/{comment}', ['body' => '<p>Changed</p>']],
    'delete' => ['DELETE', '/comments/{comment}', []],
    'react' => ['POST', '/comments/{comment}/reactions', ['emoji' => '👍']],
    'poll' => ['GET', '/tasks/{task}/comments', []],
]);

test('a stale CSRF token answers 419 JSON, not a redirect or an HTML page', function () {
    // The real check, without its "running unit tests" skip.
    $this->app->bind(PreventRequestForgery::class, fn ($app) => new class($app, $app['encrypter']) extends PreventRequestForgery
    {
        protected function runningUnitTests()
        {
            return false;
        }
    });

    $this->actingAs($this->user)
        ->withHeader('X-CSRF-TOKEN', 'stale-token')
        ->postJson('/tasks/'.$this->task->id.'/comments', ['body' => '<p>New</p>'])
        ->assertStatus(419)
        ->assertJson(['message' => 'CSRF token mismatch.']);

    expect(Comment::count())->toBe(1);
});

test('opening the task signed out goes to sign in, remembering the task', function () {
    $taskUrl = route('tasks.edit', $this->task);

    $this->get($taskUrl)->assertRedirect(route('login'));

    expect(session('url.intended'))->toBe($taskUrl);
});

test('signing in after that returns to the task', function () {
    $taskUrl = route('tasks.edit', $this->task);
    $this->get($taskUrl);

    $this->post('/login', ['identifier' => 'mona', 'password' => 'correct-password'])
        ->assertRedirect($taskUrl);

    $this->assertAuthenticatedAs($this->user);
});
