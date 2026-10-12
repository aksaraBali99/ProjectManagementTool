<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PerformanceSeeder;
use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * task #72 phase 0: `php artisan perf:baseline` — renders each heavy page
 * in-process as a real signed-in user and reports queries, time, memory
 * and response size. This is the number every later phase is measured
 * against, so it deliberately changes nothing about how the pages work.
 *
 * Read-only by construction, two ways over: the session driver is forced
 * to "array" for the run (the database driver would otherwise write a
 * session row per request), and every request is wrapped in a transaction
 * that is always rolled back, so even an unexpected write cannot survive.
 */
class PerformanceBaseline extends Command
{
    protected $signature = 'perf:baseline
        {--organization=perf-test-co : Company slug to measure}
        {--user= : Email of the user to measure as}
        {--role=management : Role to pick a user from when --user is omitted (owner|management|staff|client)}
        {--runs=3 : Requests per page; the median time is reported}
        {--memory=1G : memory_limit for the run; the Tasks list exhausts PHP\'s 128M default at 2,000 tasks}';

    protected $description = 'Measure queries, time and memory for the heavy pages (task #72 baseline)';

    /**
     * Incremented by the single query listener registered in handle().
     *
     * Deliberately a property rather than a captured local: DB::listen()
     * has no counterpart to remove a listener, so registering one per run
     * (as this command first did) left every earlier listener live. They
     * all captured the same function-scoped $count by reference, so run 2
     * counted each query twice and run 3 three times — making every
     * reported figure runs x the truth. Phase 0's local --runs=3 numbers
     * were exactly 3x the dev --runs=1 ones for this reason.
     */
    private int $queryCount = 0;

    /**
     * Artisan resolves a command once and reuses the instance, so a
     * second perf:baseline call in the same process (every test that runs
     * it twice) would otherwise stack a second listener on this same
     * counter and double every figure.
     */
    private bool $queryCounterRegistered = false;

    public function handle(): int
    {
        $organization = Organization::withoutGlobalScopes()
            ->where('slug', $this->option('organization'))->first();

        if ($organization === null) {
            $this->components->error(sprintf(
                'No company with slug "%s". Run `php artisan perf:seed` first.',
                $this->option('organization')
            ));

            return self::FAILURE;
        }

        $user = $this->resolveUser($organization);

        if ($user === null) {
            return self::FAILURE;
        }

        // Keeps the run genuinely read-only — see the class docblock.
        config(['session.driver' => 'array']);

        // The Tasks list currently exhausts PHP's default 128M at 2,000
        // tasks and dies with an uncatchable fatal, which would stop the
        // whole baseline. Raising the limit turns that from "the tool
        // crashes" into "the tool reports 968MB peak", which is the
        // finding we actually want recorded.
        //
        // Only ever RAISES: dev's CLI limit is 2048M, and the first
        // version lowered it to the 1G default and then claimed to have
        // raised it.
        $previousLimit = (string) ini_get('memory_limit');
        $raisedTo = $this->raiseMemoryLimit($previousLimit, (string) $this->option('memory'));

        $this->registerQueryCounter();

        $this->printEnvironment($organization, $user);

        $pages = [
            'Dashboard' => route('dashboard', $organization, false),
            'Kanban' => route('kanban', $organization, false),
            'Tasks list' => route('tasks.index', $organization, false),
            'Projects' => route('projects.index', $organization, false),
            'Calendar' => route('calendar', $organization, false),
        ];

        $runs = max(1, (int) $this->option('runs'));
        $rows = [];

        foreach ($pages as $label => $url) {
            $rows[] = $this->measure($label, $url, $user, $runs);
        }

        $this->newLine();
        $this->table(
            ['Page', 'URL', 'Status', 'Queries', 'Time (ms)', 'Peak memory (MB)', 'Response size (KB)'],
            $rows
        );
        $this->newLine();
        $this->components->info(sprintf('Median of %d run(s) per page; the first warms caches.', $runs));

        if ($raisedTo !== null) {
            $this->components->warn(sprintf(
                'memory_limit was raised from %s to %s for this run. A page whose peak approaches %s would fatal under the normal limit.',
                $previousLimit,
                $raisedTo,
                $previousLimit
            ));
        }

        return self::SUCCESS;
    }

    /**
     * One listener for the whole command, incrementing $queryCount, which
     * each run resets to 0. Registering per run would leave the earlier
     * listeners live and multiply every count by the run number.
     */
    private function registerQueryCounter(): void
    {
        if ($this->queryCounterRegistered) {
            return;
        }

        $this->queryCounterRegistered = true;

        DB::listen(function (): void {
            $this->queryCount++;
        });
    }

    /**
     * Raises memory_limit to $requested, and only raises it.
     *
     * @return string|null what it was raised to, or null if it was left alone
     */
    private function raiseMemoryLimit(string $current, string $requested): ?string
    {
        $currentBytes = $this->toBytes($current);
        $requestedBytes = $this->toBytes($requested);

        // -1 is unlimited: nothing can raise it, so never touch it.
        if ($currentBytes === -1) {
            return null;
        }

        if ($requestedBytes !== -1 && $requestedBytes <= $currentBytes) {
            return null;
        }

        ini_set('memory_limit', $requested);

        return $requested;
    }

    /** Parses a PHP shorthand size ("128M", "1G", "512K", "-1") to bytes. */
    private function toBytes(string $value): int
    {
        $value = trim($value);

        if ($value === '' || $value === '-1') {
            return -1;
        }

        $unit = strtolower(substr($value, -1));
        $number = (int) $value;

        return match ($unit) {
            'g' => $number * 1024 ** 3,
            'm' => $number * 1024 ** 2,
            'k' => $number * 1024,
            default => $number,
        };
    }

    /**
     * @return array<int, string>
     */
    private function measure(string $label, string $url, User $user, int $runs): array
    {
        $times = [];
        $queries = 0;
        $status = 0;
        $size = 0;
        $peak = 0.0;

        for ($run = 0; $run < $runs; $run++) {
            // Reset the shared counter rather than registering another
            // listener — see registerQueryCounter() for why that mattered.
            $this->queryCount = 0;
            memory_reset_peak_usage();

            $startedAt = microtime(true);

            // Rolled back unconditionally: a GET should write nothing, and
            // if one ever does, the baseline must not be what persists it.
            DB::beginTransaction();

            try {
                Auth::login($user);

                $request = Request::create($url, 'GET');
                $request->setUserResolver(fn () => $user);

                $kernel = app(Kernel::class);
                $response = $kernel->handle($request);

                $times[] = (microtime(true) - $startedAt) * 1000;
                $status = $response->getStatusCode();
                $size = strlen((string) $response->getContent());
                $peak = memory_get_peak_usage(true) / 1024 / 1024;

                $kernel->terminate($request, $response);
            } finally {
                DB::rollBack();
                Auth::logout();
            }

            // Read after the rollback so the counter sees the whole
            // request; the count itself is identical run to run.
            $queries = $this->queryCount;
        }

        sort($times);
        $median = $times[intdiv(count($times), 2)];

        return [
            $label,
            $url,
            $this->formatStatus($status),
            number_format($queries),
            number_format($median, 1),
            number_format($peak, 1),
            number_format($size / 1024, 1),
        ];
    }

    /**
     * A non-200 is data, not a failure: a Client legitimately cannot reach
     * everything a manager can, and knowing which pages they are refused
     * on is part of the baseline.
     */
    private function formatStatus(int $status): string
    {
        return $status === 200 ? '200' : "<fg=yellow>{$status}</>";
    }

    private function resolveUser(Organization $organization): ?User
    {
        if ($email = $this->option('user')) {
            $user = User::where('email', $email)->first();

            if ($user === null) {
                $this->components->error("No user with email \"{$email}\".");
            }

            return $user;
        }

        $role = (string) $this->option('role');

        if (in_array($role, Role::GLOBAL_SLUGS, true)) {
            $user = User::whereHas('roles', fn ($query) => $query->where('slug', $role))
                ->orderBy('id')->first();
        } else {
            $user = User::whereHas(
                'orgMemberships',
                fn ($query) => $query->where('organization_id', $organization->id)
                    ->whereHas('role', fn ($roleQuery) => $roleQuery->where('slug', $role))
            )->orderBy('id')->first();
        }

        if ($user === null) {
            $this->components->error(sprintf(
                'No "%s" user found%s. Run `php artisan perf:seed` first, or pass --user=<email>.',
                $role,
                in_array($role, Role::GLOBAL_SLUGS, true) ? '' : ' in '.$organization->name
            ));
        }

        return $user;
    }

    private function printEnvironment(Organization $organization, User $user): void
    {
        $this->newLine();
        $this->components->twoColumnDetail('<fg=cyan>APP_ENV</>', (string) app()->environment());
        $this->components->twoColumnDetail('<fg=cyan>DB driver</>', (string) DB::connection()->getDriverName());
        $this->components->twoColumnDetail('<fg=cyan>PHP</>', PHP_VERSION);
        $this->components->twoColumnDetail('<fg=cyan>memory_limit</>', (string) ini_get('memory_limit'));
        $this->components->twoColumnDetail('<fg=cyan>Company</>', $organization->name.' (#'.$organization->id.')');
        $this->components->twoColumnDetail('<fg=cyan>Measured as</>', $user->name.' <'.$user->email.'>');
        $this->components->twoColumnDetail(
            '<fg=cyan>Tasks in company</>',
            number_format(DB::table('tasks')->where('organization_id', $organization->id)->count())
        );

        if ($organization->slug !== PerformanceSeeder::COMPANY_SLUG) {
            $this->components->warn('Measuring a company other than the generated one — numbers are not comparable to the baseline.');
        }
    }
}
