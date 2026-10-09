<?php

namespace App\Console\Commands;

use Database\Seeders\PerformanceSeeder;
use Illuminate\Console\Command;

/**
 * task #72 phase 0: `php artisan perf:seed` — builds one large generated
 * company so the Task list / Kanban / Dashboard pages can be measured
 * against production-shaped data. See PerformanceSeeder for the data
 * shape and for why every write is a raw insert.
 */
class SeedPerformanceData extends Command
{
    protected $signature = 'perf:seed
        {--tasks=2000 : How many tasks to generate}
        {--comments=20000 : How many comments to spread across those tasks}
        {--users=30 : How many users (≈10% management, 70% staff, 20% client)}
        {--projects=10 : How many projects}
        {--departments=5 : How many departments}
        {--password= : Shared password for the generated users (default: random per run)}
        {--fresh : Delete the existing perf company and its users first}';

    protected $description = 'Generate a large test company for performance measurement (never run in production)';

    public function handle(): int
    {
        // No --force escape hatch on purpose: there is no legitimate reason
        // to generate thousands of fake tasks in a production database, and
        // an override would eventually be used by accident.
        if (app()->environment('production')) {
            $this->components->error('perf:seed refuses to run in production. Run it locally or on the dev site only.');

            return self::FAILURE;
        }

        $seeder = new PerformanceSeeder;

        if ($this->option('fresh')) {
            $this->components->task('Removing the existing perf company', fn () => $seeder->purge() ?? true);
        } elseif (PerformanceSeeder::companyExists()) {
            $this->components->error(sprintf(
                'The "%s" company already exists. Re-run with --fresh to replace it.',
                PerformanceSeeder::COMPANY_NAME
            ));

            return self::FAILURE;
        }

        $options = [
            'tasks' => (int) $this->option('tasks'),
            'comments' => (int) $this->option('comments'),
            'users' => (int) $this->option('users'),
            'projects' => (int) $this->option('projects'),
            'departments' => (int) $this->option('departments'),
            'password' => $this->option('password') ?: null,
        ];

        foreach ($options as $name => $value) {
            if ($name !== 'password' && $value < 1) {
                $this->components->error("--{$name} must be at least 1.");

                return self::FAILURE;
            }
        }

        $startedAt = microtime(true);
        $result = $seeder->generate($options);
        $elapsed = microtime(true) - $startedAt;

        $this->newLine();
        $this->components->info(sprintf('Generated "%s" in %.1fs', PerformanceSeeder::COMPANY_NAME, $elapsed));

        $this->table(
            ['Table', 'Rows'],
            collect($result['counts'])->map(fn ($count, $table) => [$table, number_format($count)])->values()->all()
        );

        $this->components->twoColumnDetail('<fg=yellow>Shared password</>', '<fg=yellow>'.$result['password'].'</>');
        foreach ($result['logins'] as $role => $email) {
            $this->components->twoColumnDetail(ucfirst($role).' login', $email);
        }
        $this->newLine();
        $this->components->warn('The password is shown once and is different on every run. Note it now.');

        return self::SUCCESS;
    }
}
