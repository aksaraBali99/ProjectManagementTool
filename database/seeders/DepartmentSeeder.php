<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Marketing', 'color' => '#f97316'],
            ['name' => 'Operations', 'color' => '#0ea5e9'],
            ['name' => 'Sales', 'color' => '#22c55e'],
            ['name' => 'Training', 'color' => '#eab308'],
            ['name' => 'Technology', 'color' => '#6366f1'],
            ['name' => 'Biz Dev', 'color' => '#ec4899'],
        ];

        Organization::all()->each(function (Organization $organization) use ($departments) {
            foreach ($departments as $department) {
                Department::updateOrCreate(
                    ['organization_id' => $organization->id, 'name' => $department['name']],
                    ['color' => $department['color']],
                );
            }
        });
    }
}
