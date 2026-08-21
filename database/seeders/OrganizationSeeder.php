<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = [
            ['name' => 'Bali Virtual Academy', 'slug' => 'bali-virtual-academy', 'accent_color' => '#1D9E75'],
            ['name' => 'Bali Hire', 'slug' => 'bali-hire', 'accent_color' => '#534AB7'],
            ['name' => 'Remote Works', 'slug' => 'remote-works', 'accent_color' => '#BA7517'],
        ];

        foreach ($organizations as $organization) {
            Organization::updateOrCreate(
                ['slug' => $organization['slug']],
                ['name' => $organization['name'], 'accent_color' => $organization['accent_color']],
            );
        }
    }
}
