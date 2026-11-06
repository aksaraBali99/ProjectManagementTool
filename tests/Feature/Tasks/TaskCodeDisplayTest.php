<?php

use App\Models\Department;
use App\Models\Document;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;

/**
 * task #118 phase 3: wherever a task's title is shown, its code comes
 * first ("BGS-0001 Fix invoice PDF layout"). The title itself, the URLs
 * and the edit form's Title value never change.
 */
beforeEach(function () {
    createOwner();
    $this->org = Organization::create(['name' => 'Bali Goods Sourcing', 'slug' => 'bgs', 'code' => 'BGS', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Ops', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Sourcing', 'description' => 'd']);

    $member = function (string $role): User {
        $user = User::factory()->create();
        OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $user->id, 'role_id' => Role::where('slug', $role)->firstOrFail()->id]);

        return $user;
    };
    $this->management = $member('management');
    $this->staff = $member('staff');
    $this->project->staff()->attach($this->staff->id);

    // In progress, High, due today, assigned to staff: on the Kanban, the
    // Tasks list, the drilldown, the Calendar, and every Dashboard section
    // (Active, High, and management's My Task, which lists the company's
    // staff's tasks).
    $this->task = Task::create([
        'organization_id' => $this->org->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id,
        'assignee_id' => $this->staff->id, 'title' => 'Fix invoice PDF layout',
        'priority' => 'high', 'status' => 'in_progress', 'due_date' => now()->toDateString(),
    ]);
});

/** The code span right before the title, as <x-task-code> renders it. */
function codedTitle(string $code = 'BGS-0001', string $title = 'Fix invoice PDF layout'): string
{
    return '/<span class="task-code[^"]*">'.preg_quote($code, '/').' <\/span>\s*'.preg_quote(e($title), '/').'/';
}

// --- The helpers -------------------------------------------------------

test('display_title is the code, a space and the title; the title alone without a code', function () {
    expect($this->task->display_title)->toBe('BGS-0001 Fix invoice PDF layout');

    // SQLite keeps the columns nullable, so a task can lack either part.
    DB::table('tasks')->where('id', $this->task->id)->update(['task_number' => null]);
    expect($this->task->fresh()->display_title)->toBe('Fix invoice PDF layout');

    DB::table('tasks')->where('id', $this->task->id)->update(['task_number' => 12]);
    DB::table('organizations')->where('id', $this->org->id)->update(['code' => null]);
    expect($this->task->fresh()->display_title)->toBe('Fix invoice PDF layout');
});

test('<x-task-code> renders the code, muted and unbreakable, and nothing without one', function () {
    $html = Blade::render('<x-task-code :task="$task" />Title', ['task' => $this->task]);
    expect($html)->toMatch('/^<span class="task-code whitespace-nowrap font-normal tabular-nums text-gray-500">BGS-0001 <\/span>Title$/');

    DB::table('tasks')->where('id', $this->task->id)->update(['task_number' => null]);
    expect(Blade::render('<x-task-code :task="$task" />Title', ['task' => $this->task->fresh()]))->toBe('Title');
});

test('a title is escaped next to its code, never output raw', function () {
    $this->task->update(['title' => '<b>Bold</b> & co']);

    $html = $this->actingAs($this->management)->get(route('kanban', $this->org))->assertOk()->getContent();
    expect($html)->toContain('BGS-0001 </span>&lt;b&gt;Bold&lt;/b&gt; &amp; co')
        ->not->toContain('<b>Bold</b>');

    $this->actingAs($this->management)->get(route('tasks.edit', $this->task))
        ->assertSee('<title>BGS-0001 &lt;b&gt;Bold&lt;/b&gt; &amp; co — Solava</title>', false);
});

// --- Kanban ------------------------------------------------------------

test('Kanban: the board card shows the code before the title, and not "#<id>"', function () {
    $html = $this->actingAs($this->management)->get(route('kanban', $this->org))->assertOk()->getContent();
    $card = kanbanCardMarkup($html, $this->task->id);

    expect($card)->toMatch(codedTitle())
        ->not->toMatch('/#'.$this->task->id.'\s*(&middot;|·)/');
});

test('Kanban: Load more cards show the code', function () {
    $json = $this->actingAs($this->management)
        ->getJson(route('kanban.column', [$this->org, 'in_progress']).'?offset=0')->assertOk()->json();

    expect($json['html'])->toMatch(codedTitle());
});

test('Kanban: live update cards show the code, the new one after a move to another company', function () {
    $since = now()->getTimestamp();
    $this->travel(5)->seconds();
    $this->task->update(['title' => 'Fix invoice PDF layout v2']);

    $json = $this->actingAs($this->management)
        ->getJson(route('kanban.changes', [$this->org, 'since' => $since]))->assertOk()->json();
    expect($json['cards'][0]['html'])->toMatch(codedTitle('BGS-0001', 'Fix invoice PDF layout v2'));

    // Moved to another company: its own board shows the new code.
    $other = Organization::create(['name' => 'Solava', 'slug' => 'slv', 'code' => 'SLV', 'accent_color' => '#000000']);
    $otherDept = Department::create(['organization_id' => $other->id, 'name' => 'Ops', 'color' => '#000000']);
    $otherProject = Project::create(['organization_id' => $other->id, 'name' => 'Platform', 'description' => 'd']);
    OrgMember::create(['organization_id' => $other->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);
    $this->task->update(['organization_id' => $other->id, 'project_id' => $otherProject->id, 'department_id' => $otherDept->id]);

    $html = $this->actingAs($this->management)->get(route('kanban', $other))->assertOk()->getContent();
    expect(kanbanCardMarkup($html, $this->task->id))->toMatch(codedTitle('SLV-0001', 'Fix invoice PDF layout v2'));
});

// --- Task page ---------------------------------------------------------

test('Task page (edit): tab title, header, and the code as a fixed prefix outside the Title input', function () {
    $html = $this->actingAs($this->management)->get(route('tasks.edit', $this->task))->assertOk()->getContent();

    expect($html)->toContain('<title>BGS-0001 Fix invoice PDF layout — Solava</title>')
        ->toMatch('/<h1[^>]*><span class="task-code[^"]*">BGS-0001 <\/span>&middot;\s*Edit task<\/h1>/')
        // The prefix sits beside the input; the input holds the title only.
        ->toMatch('/<span class="inline-flex select-none[^"]*bg-gray-50[^"]*"[^>]*>BGS-0001<\/span>\s*<input id="title" name="title" type="text" value="Fix invoice PDF layout"/');
});

test('Task page (edit): one Title input, with or without the code prefix', function () {
    $html = $this->actingAs($this->management)->get(route('tasks.edit', $this->task))->assertOk()->getContent();
    expect(substr_count($html, 'name="title"'))->toBe(1)
        ->and($html)->toContain('id="title-code-prefix"');

    // No code (SQLite keeps the column nullable): the same single input,
    // with no prefix and no move note.
    DB::table('tasks')->where('id', $this->task->id)->update(['task_number' => null]);
    $html = $this->actingAs($this->management)->get(route('tasks.edit', $this->task))->assertOk()->getContent();
    expect(substr_count($html, 'name="title"'))->toBe(1)
        ->and($html)->not->toContain('id="title-code-prefix"')
        ->not->toContain('id="title-code-move"')
        ->toMatch('/<input id="title" name="title" type="text" value="Fix invoice PDF layout" required\s+class="block w-full min-w-0 rounded-md /');
});

test('Task page (edit): the form knows each company\'s code, to warn that a project in another company means a new code', function () {
    $other = Organization::create(['name' => 'Solava', 'slug' => 'slv', 'code' => 'SLV', 'accent_color' => '#000000']);
    Project::create(['organization_id' => $other->id, 'name' => 'Platform', 'description' => 'd']);
    OrgMember::create(['organization_id' => $other->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);

    $html = $this->actingAs($this->management)->get(route('tasks.edit', $this->task))->assertOk()->getContent();

    // The note starts hidden, carrying the task's current company and code;
    // the script shows it when the picked project's company differs.
    expect($html)->toMatch('/<p id="title-code-move" class="[^"]*" hidden\s+data-current-organization="'.$this->org->id.'" data-current-code="BGS-0001"><\/p>/')
        ->toContain('const organizationCodes = {"'.$this->org->id.'":"BGS","'.$other->id.'":"SLV"};')
        ->toContain('This project is in another company: saving gives the task ')
        ->toContain('refreshCodeNote(orgId);');
});

test('Task page (read-only view): the code before the title', function () {
    // A client on the project sees the task read-only.
    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $client->id, 'role_id' => Role::where('slug', 'client')->firstOrFail()->id]);
    $this->project->clients()->attach($client->id);

    $html = $this->actingAs($client)->get(route('tasks.edit', $this->task))->assertOk()->getContent();

    expect($html)->not->toContain('name="title"')
        ->toContain('<title>BGS-0001 Fix invoice PDF layout — Solava</title>')
        ->toMatch('/Title<\/span><p class="mt-0.5 font-medium text-\[#1F2937\]"><span class="task-code[^"]*">BGS-0001 <\/span>Fix invoice PDF layout<\/p>/');
});

test('saving the edit form keeps tasks.title the title only, with no code written into it', function () {
    $this->actingAs($this->management)->put(route('tasks.update', $this->task), [
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'assignee_id' => $this->staff->id,
        'title' => 'Fix invoice PDF layout',
        'description' => 'd',
        'priority' => 'high',
        'status' => 'in_progress',
    ])->assertSessionHasNoErrors();

    expect(DB::table('tasks')->where('id', $this->task->id)->value('title'))->toBe('Fix invoice PDF layout')
        ->and($this->task->fresh()->code)->toBe('BGS-0001');
});

// --- The other screens -------------------------------------------------

test('Tasks list: the code before the title in the title link', function () {
    $this->actingAs($this->management)->get(route('tasks.index', $this->org))->assertOk()
        ->assertSee('class="flex-1 hover:underline"><span class="task-code whitespace-nowrap font-normal tabular-nums text-gray-500">BGS-0001 </span>Fix invoice PDF layout</a>', false);
});

test('Projects drilldown: the code before the title', function () {
    $html = $this->actingAs($this->management)->get(route('projects.tasks', $this->project))->assertOk()->getContent();

    expect($html)->toMatch(codedTitle());
});

test('Dashboard: the code before the title in every section that lists the task', function () {
    $html = $this->actingAs($this->management)->get(route('dashboard', $this->org))->assertOk()->getContent();

    // Active, High, and My Task.
    expect(preg_match_all(codedTitle(), $html))->toBe(3);
});

test('Calendar: the code before the title, and the tooltip is the display title', function () {
    $html = $this->actingAs($this->management)->get(route('calendar', $this->org))->assertOk()->getContent();

    expect($html)->toContain('title="BGS-0001 Fix invoice PDF layout"')
        ->and(preg_match_all(codedTitle(), $html))->toBeGreaterThanOrEqual(1);
});

test('Calendar: the codes cost one query, not one per task', function () {
    $count = function () {
        app()->forgetScopedInstances();

        return countQueries(fn () => $this->actingAs($this->management)->get(route('calendar', $this->org))->assertOk());
    };
    $count();
    $one = $count();

    for ($i = 0; $i < 9; $i++) {
        Task::create([
            'organization_id' => $this->org->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id,
            'title' => 'More '.$i, 'priority' => 'low', 'status' => 'pending', 'due_date' => now()->toDateString(),
        ]);
    }

    expect($count())->toBe($one);
});

test('Documents: the "Used in task" origin link has the code, and the linked-task JSON a code key', function () {
    $document = Document::create([
        'organization_id' => $this->org->id, 'uploaded_by' => $this->management->id, 'name' => 'Invoice spec',
        'link' => 'https://example.com/spec.pdf', 'access_level' => 'internal', 'origin_task_id' => $this->task->id,
    ]);
    $document->tasks()->attach($this->task->id);

    $html = $this->actingAs($this->management)->get('/documents/'.$this->org->id)->assertOk()->getContent();
    expect($html)->toMatch('/href="'.preg_quote(route('tasks.edit', $this->task->id), '/').'" class="text-brand-600 hover:underline"><span class="task-code[^"]*">BGS-0001 <\/span>Fix invoice PDF layout<\/a>/');

    $this->actingAs($this->management)->getJson("/documents/{$document->id}/linked-tasks")->assertOk()
        ->assertJsonPath('tasks.0.code', 'BGS-0001')
        ->assertJsonPath('tasks.0.title', 'Fix invoice PDF layout');

    $this->actingAs($this->management)->getJson("/documents/{$document->id}/dependencies")->assertOk()
        ->assertJsonPath('viewable_tasks.0.code', 'BGS-0001')
        ->assertJsonPath('viewable_tasks.0.title', 'Fix invoice PDF layout');
});

test('the Documents script labels tasks with their code, as text', function () {
    $html = $this->actingAs($this->management)->get('/documents/'.$this->org->id)->assertOk()->getContent();

    expect($html)->toContain("return (task.code ? task.code + ' ' : '') + task.title;")
        ->toContain('link.textContent = taskLabel(task);')
        ->not->toContain('innerHTML = taskLabel');
});
