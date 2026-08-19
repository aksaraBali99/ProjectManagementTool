<?php

namespace Database\Seeders;

use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::all()->keyBy('slug');
        $bva = Organization::where('slug', 'bali-virtual-academy')->firstOrFail();
        $baliHire = Organization::where('slug', 'bali-hire')->firstOrFail();
        $remoteWorks = Organization::where('slug', 'remote-works')->firstOrFail();

        // The founder: full cross-company access via both global roles.
        $founder = User::updateOrCreate(
            ['email' => 'founder@solva.test'],
            [
                'username' => 'founder',
                'name' => 'Founder',
                'employee_id' => 'EMP-00001',
                'phone' => '+62 811-0000-0001',
                'password' => 'password',
                'is_active' => true,
            ],
        );
        $founder->roles()->sync([$roles[Role::SUPER_ADMIN]->id, $roles[Role::OWNER]->id]);

        // Management in two companies, staff (department-scoped) in the third.
        $alex = User::updateOrCreate(
            ['email' => 'alex.management@solva.test'],
            [
                'username' => 'alex.management',
                'name' => 'Alex Wibowo',
                'employee_id' => 'EMP-00002',
                'phone' => '+62 811-0000-0002',
                'password' => 'password',
                'is_active' => true,
            ],
        );
        OrgMember::updateOrCreate(
            ['organization_id' => $bva->id, 'user_id' => $alex->id],
            ['role_id' => $roles[Role::MANAGEMENT]->id],
        );
        OrgMember::updateOrCreate(
            ['organization_id' => $baliHire->id, 'user_id' => $alex->id],
            ['role_id' => $roles[Role::MANAGEMENT]->id],
        );
        OrgMember::updateOrCreate(
            ['organization_id' => $remoteWorks->id, 'user_id' => $alex->id],
            ['role_id' => $roles[Role::STAFF]->id],
        );
        foreach (['Marketing', 'Technology'] as $departmentName) {
            $department = Department::where('organization_id', $remoteWorks->id)->where('name', $departmentName)->firstOrFail();
            AccessPermission::updateOrCreate(
                ['user_id' => $alex->id, 'organization_id' => $remoteWorks->id, 'department_id' => $department->id],
                ['allowed' => true],
            );
        }

        // Single-company staff, with access to only some departments (partial access, by design).
        $sam = User::updateOrCreate(
            ['email' => 'sam.staff@solva.test'],
            [
                'username' => 'sam.staff',
                'name' => 'Sam Prasetyo',
                'employee_id' => 'EMP-00003',
                'phone' => '+62 811-0000-0003',
                'password' => 'password',
                'is_active' => true,
            ],
        );
        OrgMember::updateOrCreate(
            ['organization_id' => $baliHire->id, 'user_id' => $sam->id],
            ['role_id' => $roles[Role::STAFF]->id],
        );
        foreach (['Sales', 'Operations'] as $departmentName) {
            $department = Department::where('organization_id', $baliHire->id)->where('name', $departmentName)->firstOrFail();
            AccessPermission::updateOrCreate(
                ['user_id' => $sam->id, 'organization_id' => $baliHire->id, 'department_id' => $department->id],
                ['allowed' => true],
            );
        }

        // Single-company management (contrast with Alex's multi-company spread).
        $maya = User::updateOrCreate(
            ['email' => 'maya.manager@solva.test'],
            [
                'username' => 'maya.manager',
                'name' => 'Maya Santoso',
                'employee_id' => 'EMP-00004',
                'phone' => '+62 811-0000-0004',
                'password' => 'password',
                'is_active' => true,
            ],
        );
        OrgMember::updateOrCreate(
            ['organization_id' => $remoteWorks->id, 'user_id' => $maya->id],
            ['role_id' => $roles[Role::MANAGEMENT]->id],
        );
    }
}
