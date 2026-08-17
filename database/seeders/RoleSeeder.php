<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Admin', 'slug' => Role::SUPER_ADMIN, 'description' => 'Full access to every company, everything.'],
            ['name' => 'Owner', 'slug' => Role::OWNER, 'description' => 'Full access, plus company/department/role administration.'],
            ['name' => 'Management', 'slug' => Role::MANAGEMENT, 'description' => 'Full visibility across all departments within their company/companies.'],
            ['name' => 'Staff', 'slug' => Role::STAFF, 'description' => 'Sees tasks only in departments they are granted access to.'],
            ['name' => 'Client', 'slug' => Role::CLIENT, 'description' => 'Sees progress only on projects where they are the listed client.'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                ['name' => $role['name'], 'description' => $role['description'], 'is_system' => true],
            );
        }
    }
}
