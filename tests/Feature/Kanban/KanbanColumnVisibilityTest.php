<?php

use App\Enums\TaskStatus;
use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;

/**
 * task #72 phase 5: capping the Kanban columns and adding Load more must
 * not change who sees which card, or what a card lets them do.
 *
 * - The cards across the board plus every Load more are exactly
 *   Task::visibleTo() for that status, for every viewer shape.
 * - The Load more endpoint has the board's own access rule.
 * - A card renders identically whether it arrives on the board or from
 *   Load more: same draggable / status select (canEdit), same assignee
 *   select (canReassign), same assignee options.
 */
beforeEach(function () {
    createOwner();

    $this->org = Organization::create(['name' => 'Board Org', 'slug' => 'board-org', 'accent_color' => '#1D9E75']);
    $this->otherOrg = Organization::create(['name' => 'Other Org', 'slug' => 'other-org', 'accent_color' => '#534AB7']);

    $this->deptA = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->deptB = Department::create(['organization_id' => $this->org->id, 'name' => 'Operations', 'color' => '#000000']);
    $this->projectA = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);
    $this->projectB = Project::create(['organization_id' => $this->org->id, 'name' => 'Project B', 'description' => 'd']);

    $member = function (string $role, ?Organization $org = null) {
        $user = User::factory()->create();
        OrgMember::create([
            'organization_id' => ($org ?? $this->org)->id,
            'user_id' => $user->id,
            'role_id' => Role::where('slug', $role)->firstOrFail()->id,
        ]);

        return $user;
    };
    $grant = fn (User $user, Department $department) => AccessPermission::create([
        'user_id' => $user->id, 'organization_id' => $this->org->id, 'department_id' => $department->id, 'allowed' => true,
    ]);

    $this->management = $member('management');
    $this->staffWithAccess = $member('staff');
    $grant($this->staffWithAccess, $this->deptA);
    $this->staffWithOtherAccess = $member('staff');
    $grant($this->staffWithOtherAccess, $this->deptB);
    $this->staffWithoutAccess = $member('staff');
    $this->staffAssignee = $member('staff');
    $this->staffSubtaskAssignee = $member('staff');
    $this->client = $member('client');
    $this->projectA->clients()->attach($this->client->id);
    $this->otherManagement = $member('management', $this->otherOrg);

    // On both projects, so the assignee dropdowns have real options.
    foreach ([$this->staffWithAccess, $this->staffWithOtherAccess, $this->staffAssignee] as $staff) {
        $this->projectA->staff()->attach($staff->id);
        $this->projectB->staff()->attach($staff->id);
    }

    $statuses = array_map(fn (TaskStatus $s) => $s->value, TaskStatus::cases());
    $assignees = [null, $this->staffWithAccess->id, $this->staffAssignee->id, $this->management->id, $this->staffWithOtherAccess->id];

    // 45 per status: every column needs Load more for management, and the
    // narrower viewers still see a mix.
    for ($i = 0; $i < 180; $i++) {
        $task = Task::create([
            'organization_id' => $this->org->id,
            // 3 in 4 on Project A, so the client (on Project A only) also
            // has more than 30 cards in a column.
            'project_id' => $i % 4 ? $this->projectA->id : $this->projectB->id,
            'department_id' => $i % 3 ? $this->deptA->id : $this->deptB->id,
            'assignee_id' => $assignees[$i % count($assignees)],
            'title' => 'Card '.$i,
            'priority' => ['high', 'medium', 'low'][$i % 3],
            'status' => $statuses[intdiv($i, 45)],
            'due_date' => $i % 4 ? '2026-11-'.str_pad((string) (($i % 28) + 1), 2, '0', STR_PAD_LEFT) : null,
        ]);

        if ($i % 37 === 0) {
            Subtask::create(['task_id' => $task->id, 'title' => 'Sub', 'assignee_id' => $this->staffSubtaskAssignee->id]);
        }

        if ($i % 50 === 7) {
            // Inactive: never on the board.
            $task->delete();
        }
    }

    // Low priority with no due date sorts last in Pending for everyone, so
    // it always arrives by Load more: a card staffWithAccess may move
    // (their own) in the same batch as cards they may not.
    Task::create([
        'organization_id' => $this->org->id, 'project_id' => $this->projectA->id, 'department_id' => $this->deptA->id,
        'assignee_id' => $this->staffWithAccess->id, 'title' => 'Last in Pending', 'priority' => 'low', 'status' => 'pending',
    ]);

    // Another company's card must never appear.
    $otherDept = Department::create(['organization_id' => $this->otherOrg->id, 'name' => 'Sales', 'color' => '#000000']);
    $otherProject = Project::create(['organization_id' => $this->otherOrg->id, 'name' => 'Elsewhere', 'description' => 'd']);
    Task::create([
        'organization_id' => $this->otherOrg->id, 'project_id' => $otherProject->id, 'department_id' => $otherDept->id,
        'assignee_id' => $this->staffAssignee->id, 'title' => 'Elsewhere', 'priority' => 'high', 'status' => 'pending',
    ]);
});

dataset('kanban viewers', [
    'management' => ['management'],
    'staff with department access' => ['staffWithAccess'],
    'staff with access to the other department only' => ['staffWithOtherAccess'],
    'staff without any department access' => ['staffWithoutAccess'],
    'staff who is only a task assignee' => ['staffAssignee'],
    'staff who is only a subtask assignee' => ['staffSubtaskAssignee'],
    'client' => ['client'],
]);

test('the cards across the board and every Load more are exactly what visibleTo() allows, per column', function (string $viewer) {
    $user = $this->{$viewer};

    foreach (TaskStatus::cases() as $status) {
        $expected = Task::visibleTo($user, $this->org->id)->where('status', $status->value)->pluck('id')->sort()->values()->all();

        $shown = kanbanColumnIdsAcrossBatches($user, $this->org->id, $status);

        expect($shown)->toHaveCount(count(array_unique($shown)), "{$viewer} / {$status->value}: a card appeared twice");
        sort($shown);
        expect($shown)->toBe($expected, "{$viewer} / {$status->value}");
    }
})->with('kanban viewers');

test('the dataset gives the viewers different, non-trivial slices, with Load more in play', function () {
    $count = fn (User $user) => Task::visibleTo($user, $this->org->id)->count();

    expect($count($this->management))->toBeGreaterThan(4 * 30)
        ->and($count($this->staffWithAccess))->toBeGreaterThan(30)->toBeLessThan($count($this->management))
        ->and($count($this->staffAssignee))->toBeGreaterThan(0)->toBeLessThan($count($this->staffWithAccess))
        ->and($count($this->staffSubtaskAssignee))->toBeGreaterThan(0)->toBeLessThan(10)
        ->and($count($this->client))->toBeGreaterThan(30)->toBeLessThan($count($this->management));
});

test('the Load more endpoint refuses who the board refuses', function () {
    $url = route('kanban.column', [$this->org, 'pending']);

    // Another company's management.
    $this->actingAs($this->otherManagement)->getJson($url)->assertForbidden();

    // view_kanban revoked from the staff role.
    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    $staffRole->permissions()->sync($staffRole->permissions()->where('slug', '!=', 'view_kanban')->pluck('permissions.id')->all());
    $this->actingAs($this->staffWithAccess)->getJson($url)->assertForbidden();

    // A deactivated company.
    $this->org->update(['is_active' => false]);
    $this->actingAs($this->management)->getJson($url)->assertForbidden();
    $this->org->update(['is_active' => true]);

    // Guests.
    auth()->logout();
    $this->getJson($url)->assertUnauthorized();
});

test('an unknown status or company is a 404', function () {
    $this->actingAs($this->management)->getJson(route('kanban', $this->org).'/columns/not_a_status')->assertNotFound();
    $this->actingAs($this->management)->getJson('/kanban/999999/columns/pending')->assertNotFound();
});

/** One card's markup, normalised through the same DOM parser for both sources. */
function kanbanCardMarkup(string $html, int $taskId): string
{
    $document = new DOMDocument;
    libxml_use_internal_errors(true);
    $document->loadHTML('<?xml encoding="utf-8" ?><body>'.$html.'</body>');
    libxml_clear_errors();

    $node = (new DOMXPath($document))
        ->query('//div[contains(concat(" ", @class, " "), " kanban-card ") and @data-task-id="'.$taskId.'"]')
        ->item(0);
    expect($node)->not->toBeNull("card {$taskId} not found");

    return $document->saveHTML($node);
}

test('a card from Load more is identical to the same card on the board: permissions and assignee options', function (string $viewer) {
    $user = $this->{$viewer};

    // Everything past the 30th card of Pending arrives via Load more.
    $order = kanbanColumnIdsAcrossBatches($user, $this->org->id, TaskStatus::Pending);
    expect(count($order))->toBeGreaterThan(30);
    $loadedIds = array_slice($order, 30, 30);

    $json = $this->actingAs($user)->getJson(route('kanban.column', [$this->org, 'pending']).'?offset=30')->assertOk()->json();
    $fromLoadMore = collect($loadedIds)->mapWithKeys(fn ($id) => [$id => kanbanCardMarkup($json['html'], $id)]);

    // Deactivate the cards in front of them, so the same cards now render
    // on the board itself. Nothing a card shows depends on its position.
    Task::whereIn('id', array_slice($order, 0, 30))->get()->each->delete();

    $board = $this->actingAs($user)->get(route('kanban', $this->org))->assertOk()->getContent();

    foreach ($fromLoadMore as $id => $markup) {
        expect(kanbanCardMarkup($board, $id))->toBe($markup, "card {$id} differs for {$viewer}");
    }

    // The batch genuinely mixes what the viewer may and may not do, so the
    // comparison above covers both states.
    $batchHtml = $fromLoadMore->implode('');
    expect($batchHtml)->toContain('kanban-assignee-select');
    if ($viewer === 'staffWithAccess') {
        expect($batchHtml)->toContain('draggable="true"')->toContain('draggable="false"');
    }
})->with([
    'management' => ['management'],
    'staff with department access' => ['staffWithAccess'],
    'client' => ['client'],
]);
