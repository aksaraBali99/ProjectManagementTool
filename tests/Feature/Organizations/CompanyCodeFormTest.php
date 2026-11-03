<?php

use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * task #118 phase 2: the company's task code (BVA, as in BVA-0001) on the
 * Companies pages — set on Add company, editable until the company has
 * issued a task number, then locked.
 */
beforeEach(function () {
    $this->owner = createOwner();
});

function companyForm(array $overrides = []): array
{
    return array_merge(['name' => 'Bali Virtual Academy', 'code' => 'BVA', 'accent_color' => '#1D9E75'], $overrides);
}

function makeCompany(string $name, string $code, array $attributes = []): Organization
{
    return Organization::create(array_merge(['name' => $name, 'slug' => str()->slug($name), 'code' => $code, 'accent_color' => '#1D9E75'], $attributes));
}

function giveCompanyATask(Organization $organization): Task
{
    $department = Department::create(['organization_id' => $organization->id, 'name' => 'Ops '.uniqid(), 'color' => '#000000']);
    $project = Project::create(['organization_id' => $organization->id, 'name' => 'Project '.uniqid(), 'description' => 'd']);

    return Task::create([
        'organization_id' => $organization->id, 'project_id' => $project->id, 'department_id' => $department->id,
        'title' => 'A task', 'priority' => 'medium', 'status' => 'pending',
    ]);
}

// --- Add company --------------------------------------------------------

test('the Add company form has the task code field with its help text, preview and suggestion URL', function () {
    $this->actingAs($this->owner)->get(route('organizations.create'))->assertOk()
        ->assertSee('name="code"', false)
        ->assertSee('maxlength="3"', false)
        ->assertSee("3 letters, shown before every task number, e.g. BVA-0001. It can't be changed once the company has tasks.", false)
        ->assertSee('id="code-preview"', false)
        ->assertSee('data-suggest-on-blur', false)
        ->assertSee('data-suggest-url="'.route('organizations.code-suggestion').'"', false);
});

test('a valid code is saved', function () {
    $this->actingAs($this->owner)->post(route('organizations.store'), companyForm())->assertRedirect(route('organizations.index'));

    expect(Organization::where('name', 'Bali Virtual Academy')->value('code'))->toBe('BVA');
});

test('lowercase and spaced input is saved uppercase', function (string $typed) {
    $this->actingAs($this->owner)->post(route('organizations.store'), companyForm(['code' => $typed]))->assertRedirect();

    expect(Organization::where('name', 'Bali Virtual Academy')->value('code'))->toBe('BVA');
})->with(['lowercase' => ['bva'], 'mixed case' => ['bVa'], 'spaces around' => [' bva ']]);

test('a code that is not exactly 3 letters is rejected with the message', function (string $code) {
    $this->actingAs($this->owner)->post(route('organizations.store'), companyForm(['code' => $code]))
        ->assertSessionHasErrors(['code' => 'The task code must be exactly 3 letters (A to Z).']);

    expect(Organization::where('name', 'Bali Virtual Academy')->exists())->toBeFalse();
})->with(['2 letters' => ['BV'], '4 letters' => ['BVAX'], 'digits' => ['B1A'], 'symbols' => ['B-A'], 'accented' => ['BVÁ'], 'inner space' => ['B A']]);

test('a code another company uses is rejected, naming that company, even when it is deactivated', function (bool $active) {
    makeCompany('Bali Virtual Academy', 'BVA', ['is_active' => $active]);

    $this->actingAs($this->owner)->post(route('organizations.store'), companyForm(['name' => 'Bvatech', 'code' => 'bva']))
        ->assertSessionHasErrors(['code' => 'BVA is already used by Bali Virtual Academy.']);

    expect(Organization::where('name', 'Bvatech')->exists())->toBeFalse();
})->with(['active company' => [true], 'deactivated company' => [false]]);

test('the form requires the code, while a company created in code without one (the import path) still gets the suggestion', function () {
    $this->actingAs($this->owner)->post(route('organizations.store'), ['name' => 'Bali Hire', 'accent_color' => '#1D9E75'])
        ->assertSessionHasErrors(['code' => 'The task code is required.']);
    $this->actingAs($this->owner)->post(route('organizations.store'), companyForm(['name' => 'Bali Hire', 'code' => '   ']))
        ->assertSessionHasErrors(['code' => 'The task code is required.']);
    expect(Organization::where('name', 'Bali Hire')->exists())->toBeFalse();

    expect(Organization::create(['name' => 'Bali Hire', 'slug' => 'bali-hire', 'accent_color' => '#000000'])->code)->toBe('BAH');
});

test('two owners saving the same code at once: the second gets the "already used" error, not a 500', function () {
    // Another owner's company with the same code lands between this
    // request's validation and its insert.
    Organization::creating(function (Organization $organization) {
        if ((string) $organization->name === 'Bali Virtual Academy') {
            DB::table('organizations')->insert([
                'name' => 'Rival Academy', 'slug' => 'rival-academy', 'code' => $organization->code, 'accent_color' => '#000000',
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    });

    $this->actingAs($this->owner)->post(route('organizations.store'), companyForm())
        ->assertSessionHasErrors(['code' => 'BVA is already used by Rival Academy.']);

    expect(Organization::where('name', 'Bali Virtual Academy')->exists())->toBeFalse();
});

// --- Edit company -------------------------------------------------------

test('a company that has never issued a task number has an editable code, with "Suggest from name"', function () {
    $company = makeCompany('Bali Hire', 'BAL');

    $this->actingAs($this->owner)->get(route('organizations.edit', $company))->assertOk()
        ->assertSee('name="code"', false)
        ->assertSee('value="BAL"', false)
        ->assertSee('Suggest from name')
        ->assertSee('data-except="'.$company->id.'"', false)
        ->assertDontSee('Locked:');
});

test('an unlocked code can change, and saving its own code again is not "already used"', function () {
    $company = makeCompany('Bali Hire', 'BAL');

    $this->actingAs($this->owner)->put(route('organizations.update', $company), companyForm(['name' => 'Bali Hire', 'code' => 'bah']))
        ->assertRedirect(route('organizations.index'));
    expect($company->fresh()->code)->toBe('BAH');

    $this->actingAs($this->owner)->put(route('organizations.update', $company), companyForm(['name' => 'Bali Hire Co', 'code' => 'BAH']))
        ->assertSessionHasNoErrors();
    expect($company->fresh()->name)->toBe('Bali Hire Co')->and($company->fresh()->code)->toBe('BAH');
});

test('an unlocked code still has to be valid, present, and not another company\'s', function () {
    makeCompany('Bali Virtual Academy', 'BVA');
    $company = makeCompany('Bali Hire', 'BAL');

    $this->actingAs($this->owner)->put(route('organizations.update', $company), companyForm(['name' => 'Bali Hire', 'code' => 'BVA']))
        ->assertSessionHasErrors(['code' => 'BVA is already used by Bali Virtual Academy.']);
    $this->actingAs($this->owner)->put(route('organizations.update', $company), companyForm(['name' => 'Bali Hire', 'code' => 'B4']))
        ->assertSessionHasErrors(['code' => 'The task code must be exactly 3 letters (A to Z).']);
    $this->actingAs($this->owner)->put(route('organizations.update', $company), ['name' => 'Bali Hire', 'accent_color' => '#1D9E75'])
        ->assertSessionHasErrors(['code' => 'The task code is required.']);

    expect($company->fresh()->code)->toBe('BAL');
});

test('renaming a company never changes its code', function () {
    $company = makeCompany('Bali Hire', 'BAL');

    $this->actingAs($this->owner)->put(route('organizations.update', $company), companyForm(['name' => 'Solava', 'code' => 'BAL']))
        ->assertSessionHasNoErrors();

    expect($company->fresh()->code)->toBe('BAL');
});

dataset('locked companies', [
    'has a task' => [function () {
        $company = makeCompany('Bali Virtual Academy', 'BVA');
        giveCompanyATask($company);
        giveCompanyATask($company);

        return [$company, 'BVA-0002'];
    }],
    'its only task was moved to another company' => [function () {
        $company = makeCompany('Bali Virtual Academy', 'BVA');
        $other = makeCompany('Solava', 'SLV');
        $task = giveCompanyATask($company);
        $otherProject = Project::create(['organization_id' => $other->id, 'name' => 'Elsewhere', 'description' => 'd']);
        $otherDepartment = Department::create(['organization_id' => $other->id, 'name' => 'Ops', 'color' => '#000000']);
        $task->update(['organization_id' => $other->id, 'project_id' => $otherProject->id, 'department_id' => $otherDepartment->id]);
        expect(Task::withTrashed()->where('organization_id', $company->id)->count())->toBe(0);

        return [$company, 'BVA-0001'];
    }],
    'its only task is deactivated' => [function () {
        $company = makeCompany('Bali Virtual Academy', 'BVA');
        giveCompanyATask($company)->delete();
        expect(Task::where('organization_id', $company->id)->count())->toBe(0);

        return [$company, 'BVA-0001'];
    }],
]);

test('a company that has issued task numbers shows its code as read-only text with the lock message', function (Closure $make) {
    [$company, $last] = $make();
    $company->refresh();

    expect($company->hasIssuedTaskNumbers())->toBeTrue();

    $this->actingAs($this->owner)->get(route('organizations.edit', $company))->assertOk()
        ->assertDontSee('name="code"', false)
        ->assertDontSee('Suggest from name')
        ->assertSee('>BVA</span>', false)
        ->assertSee("Locked: this company has issued task numbers up to {$last}, so the code can't change.", false);
})->with('locked companies');

test('a locked code cannot change, but the same code, no code, and name or accent changes still save', function (Closure $make) {
    [$company] = $make();
    $company->refresh();

    $this->actingAs($this->owner)->put(route('organizations.update', $company), companyForm(['code' => 'BVX']))
        ->assertSessionHasErrors(['code' => "The task code can't change once the company has tasks."]);
    expect($company->fresh()->code)->toBe('BVA');

    $this->actingAs($this->owner)->put(route('organizations.update', $company), companyForm(['code' => ' bva ', 'name' => 'BVA Renamed']))
        ->assertSessionHasNoErrors();
    expect($company->fresh()->name)->toBe('BVA Renamed');

    // The edit form sends no code at all once it's locked.
    $this->actingAs($this->owner)->put(route('organizations.update', $company), ['name' => 'Bali Virtual Academy', 'accent_color' => '#534AB7'])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('organizations.index'));

    expect($company->fresh()->name)->toBe('Bali Virtual Academy')
        ->and($company->fresh()->accent_color)->toBe('#534AB7')
        ->and($company->fresh()->code)->toBe('BVA');
})->with('locked companies');

test('a brand-new company is not locked', function () {
    expect(makeCompany('Bali Hire', 'BAH')->fresh()->hasIssuedTaskNumbers())->toBeFalse();
});

// --- Suggestion endpoint ------------------------------------------------

test('the suggestion endpoint returns the suggested code for the name', function (string $name, string $code) {
    $this->actingAs($this->owner)->getJson(route('organizations.code-suggestion', ['name' => $name]))
        ->assertOk()
        ->assertExactJson(['code' => $code]);
})->with([
    ['Bali Virtual Academy', 'BVA'],
    ['Bali Hire', 'BAH'],
    ['Solava', 'SLV'],
    ['', 'XXX'],
    ['!!! ???', 'XXX'],
]);

test('the suggestion skips codes in use, deactivated companies included, except the company being edited', function () {
    $academy = makeCompany('Bali Virtual Academy', 'BVA', ['is_active' => false]);

    $this->actingAs($this->owner)->getJson(route('organizations.code-suggestion', ['name' => 'Bali Virtual Academy']))
        ->assertExactJson(['code' => 'BAL']);

    $this->actingAs($this->owner)->getJson(route('organizations.code-suggestion', ['name' => 'Bali Virtual Academy', 'except' => $academy->id]))
        ->assertExactJson(['code' => 'BVA']);
});

test('the suggestion endpoint, like the Companies pages, is for owners and super admins only', function () {
    $company = makeCompany('Bali Virtual Academy', 'BVA');
    $manager = User::factory()->create();
    OrgMember::create(['organization_id' => $company->id, 'user_id' => $manager->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);

    $this->actingAs($manager)->getJson(route('organizations.code-suggestion', ['name' => 'Bali Hire']))->assertForbidden();

    auth()->logout();
    $this->getJson(route('organizations.code-suggestion', ['name' => 'Bali Hire']))->assertUnauthorized();
});

// --- Companies list -----------------------------------------------------

test('the Companies list shows each company\'s code, with its mobile label', function () {
    makeCompany('Bali Virtual Academy', 'BVA');
    makeCompany('Solava', 'SLV', ['is_active' => false]);

    $html = $this->actingAs($this->owner)->get(route('organizations.index'))->assertOk()->getContent();

    expect($html)->toMatch('/<th[^>]*>\s*Name\s*<\/th>\s*<th[^>]*>\s*Code\s*<\/th>/')
        ->toMatch('/md:hidden">Code<\/span>\s*<span class="rounded-sm[^"]*font-mono[^"]*">BVA<\/span>/')
        ->toContain('>SLV</span>');
});
