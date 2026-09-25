<?php

use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;

/**
 * task #71: several small visual/layout tweaks to the Kanban card built in
 * Phase 5 — Assignee moved onto the same row as Status (was its own row
 * below), the Assignee select's width fixed (was sizing to fit whichever
 * name was selected), the avatar's right edge aligned with the Status
 * select's right edge, and the task's own database id shown as "#<id>".
 *
 * Pest has no browser/layout engine, so it can't measure actual rendered
 * pixel positions — what these tests pin down instead is the DOM
 * STRUCTURE that produces the visual result: Assignee and Status as
 * siblings in one row (not two rows), the same fixed width class on the
 * select regardless of name length, and the avatar's row + the status
 * row both being direct children of the card with Status last in its own
 * row (so its right edge is that row's right edge, matching the avatar's
 * row's right edge — see kanban.blade.php's own comments for the full
 * reasoning). The actual "does this look aligned" check was done in a
 * real browser per the PR description.
 */
beforeEach(function () {
    $this->owner = createOwner();

    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->first()->id,
    ]);
});

function makeKanbanCardAssignee(Organization $org, Department $department, Project $project, string $name): User
{
    $user = User::factory()->create(['name' => $name]);
    OrgMember::create([
        'organization_id' => $org->id,
        'user_id' => $user->id,
        'role_id' => Role::where('slug', 'staff')->first()->id,
    ]);
    AccessPermission::create([
        'user_id' => $user->id,
        'organization_id' => $org->id,
        'department_id' => $department->id,
        'allowed' => true,
    ]);
    $project->staff()->attach($user->id);

    return $user;
}

function findKanbanCard(string $page, int $taskId): ?DOMElement
{
    $document = new DOMDocument;
    libxml_use_internal_errors(true);
    $document->loadHTML('<?xml encoding="utf-8" ?>'.$page);
    libxml_clear_errors();

    $xpath = new DOMXPath($document);
    foreach ($xpath->query('//*[contains(@class, "kanban-card")]') as $card) {
        if ($card->getAttribute('data-task-id') === (string) $taskId) {
            return $card;
        }
    }

    return null;
}

test('the Assignee select and the Status select render as siblings in the same row, Assignee first', function () {
    $assignee = makeKanbanCardAssignee($this->org, $this->dept, $this->project, 'Ann Smith');
    $task = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'assignee_id' => $assignee->id,
        'title' => 'Ship it',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $page = $this->actingAs($this->management)->get('/kanban/'.$this->org->id)->assertOk()->getContent();
    $card = findKanbanCard($page, $task->id);
    expect($card)->not->toBeNull();

    $document = $card->ownerDocument;
    $xpath = new DOMXPath($document);
    $assigneeSelect = $xpath->query('.//select[contains(@class, "kanban-assignee-select")]', $card)->item(0);
    $statusSelect = $xpath->query('.//select[contains(@class, "kanban-status-select")]', $card)->item(0);

    expect($assigneeSelect)->not->toBeNull();
    expect($statusSelect)->not->toBeNull();
    // Same parent element — i.e. actually in the same row, not merely
    // both present somewhere on the card.
    expect($assigneeSelect->parentNode)->toBe($statusSelect->parentNode);

    // Assignee comes first (Status is last in the row — see the "avatar
    // aligns with Status" test below for why the order itself matters).
    $row = $assigneeSelect->parentNode;
    $selectsInRow = [];
    foreach ($row->childNodes as $child) {
        if ($child instanceof DOMElement && $child->tagName === 'select') {
            $selectsInRow[] = $child->getAttribute('class');
        }
    }
    expect($selectsInRow)->toHaveCount(2);
    expect($selectsInRow[0])->toContain('kanban-assignee-select');
    expect($selectsInRow[1])->toContain('kanban-status-select');
});

test('the Assignee select has the same fixed width regardless of whether the assignee\'s name is short or long', function () {
    $shortNamed = makeKanbanCardAssignee($this->org, $this->dept, $this->project, 'Al');
    $longNamed = makeKanbanCardAssignee($this->org, $this->dept, $this->project, 'Christopher Alexander Montgomery');

    $shortTask = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'assignee_id' => $shortNamed->id,
        'title' => 'Short name task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $longTask = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'assignee_id' => $longNamed->id,
        'title' => 'Long name task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $page = $this->actingAs($this->management)->get('/kanban/'.$this->org->id)->assertOk()->getContent();

    $shortCard = findKanbanCard($page, $shortTask->id);
    $longCard = findKanbanCard($page, $longTask->id);
    expect($shortCard)->not->toBeNull();
    expect($longCard)->not->toBeNull();

    // findKanbanCard() parses the page into its own fresh DOMDocument each
    // call, so $shortCard and $longCard belong to two different documents
    // — each needs its own DOMXPath bound to its own document.
    $shortSelect = (new DOMXPath($shortCard->ownerDocument))->query('.//select[contains(@class, "kanban-assignee-select")]', $shortCard)->item(0);
    $longSelect = (new DOMXPath($longCard->ownerDocument))->query('.//select[contains(@class, "kanban-assignee-select")]', $longCard)->item(0);

    // Identical class list — including the fixed width utility — for
    // both, regardless of the wildly different name lengths. This is the
    // actual bug being fixed: the select's own width used to vary with
    // whichever name happened to be selected.
    expect($shortSelect->getAttribute('class'))->toBe($longSelect->getAttribute('class'));
    expect($shortSelect->getAttribute('class'))->toContain('w-20');
});

test('a long assignee name truncates with an ellipsis (the truncate utility) rather than expanding the dropdown', function () {
    $longNamed = makeKanbanCardAssignee($this->org, $this->dept, $this->project, 'Christopher Alexander Montgomery');
    $task = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'assignee_id' => $longNamed->id,
        'title' => 'Long name task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $page = $this->actingAs($this->management)->get('/kanban/'.$this->org->id)->assertOk()->getContent();
    $card = findKanbanCard($page, $task->id);
    $xpath = new DOMXPath($card->ownerDocument);
    $select = $xpath->query('.//select[contains(@class, "kanban-assignee-select")]', $card)->item(0);

    // Tailwind's `truncate` utility (overflow: hidden; text-overflow:
    // ellipsis; white-space: nowrap) — the same one Calendar's own task
    // labels already use for exactly this (Phase 8) — combined with the
    // fixed width from the test above is what makes a long name clip
    // with an ellipsis instead of stretching the control.
    expect($select->getAttribute('class'))->toContain('truncate');
    // The full name is still available on hover — both the select's own
    // title and the avatar's (avatar.blade.php always sets one).
    expect($select->getAttribute('title'))->toBe('Christopher Alexander Montgomery');
});

test('the avatar and the Status select both sit at the same right-hand boundary of the card — same parent width, Status last in its row', function () {
    $assignee = makeKanbanCardAssignee($this->org, $this->dept, $this->project, 'Ann Smith');
    $task = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'assignee_id' => $assignee->id,
        'title' => 'Ship it',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $page = $this->actingAs($this->management)->get('/kanban/'.$this->org->id)->assertOk()->getContent();
    $card = findKanbanCard($page, $task->id);
    $xpath = new DOMXPath($card->ownerDocument);

    // The avatar's own row and the priority/assignee/status row are both
    // DIRECT children of the card (no extra nested wrapper offsetting
    // one relative to the other), so both stretch to the same width and
    // share the same right-hand boundary.
    $directRows = $xpath->query('./div', $card);
    expect($directRows->length)->toBe(2);

    $titleRow = $directRows->item(0);
    $statusRow = $directRows->item(1);

    $avatar = $xpath->query('.//span[contains(@class, "rounded-full")]', $titleRow)->item(0);
    expect($avatar)->not->toBeNull();
    expect($avatar->getAttribute('title'))->toBe('Ann Smith');

    // Status is the LAST select in its own row, so its right edge IS
    // that row's right edge — matching the avatar row's right edge
    // (both rows share the same parent and horizontal padding).
    $selectsInRow = $xpath->query('.//select', $statusRow);
    expect($selectsInRow->length)->toBe(2);
    expect($selectsInRow->item($selectsInRow->length - 1)->getAttribute('class'))->toContain('kanban-status-select');
});

test('every Kanban card shows its task\'s own database id as "#<id>", not the Import feature\'s file-scoped Task Ref numbering', function () {
    $task = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Ship it',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $page = $this->actingAs($this->management)->get('/kanban/'.$this->org->id)->assertOk()->getContent();
    $card = findKanbanCard($page, $task->id);
    expect($card)->not->toBeNull();

    expect($card->ownerDocument->saveHTML($card))->toContain('#'.$task->id);
});

test('two different tasks show two different ids, each matching that specific task\'s own real database id', function () {
    $taskOne = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'First task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $taskTwo = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Second task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $page = $this->actingAs($this->management)->get('/kanban/'.$this->org->id)->assertOk()->getContent();

    $cardOne = findKanbanCard($page, $taskOne->id);
    $cardTwo = findKanbanCard($page, $taskTwo->id);

    expect($cardOne->ownerDocument->saveHTML($cardOne))->toContain('#'.$taskOne->id);
    expect($cardTwo->ownerDocument->saveHTML($cardTwo))->toContain('#'.$taskTwo->id);
    expect($taskOne->id)->not->toBe($taskTwo->id);
});
