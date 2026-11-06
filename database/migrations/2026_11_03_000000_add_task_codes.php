<?php

use App\Support\CompanyCode;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * task #118 phase 1: company-coded task numbers (BGS-0012).
     *
     * organizations gets a 3-letter `code` and a `next_task_number`
     * counter; tasks get a per-company `task_number`. Existing tasks are
     * numbered 1..N per company in id order (oldest first), soft-deleted
     * ones included so their numbers stay used, and each counter is set
     * to max + 1.
     *
     * Codes come from this fixed map, keyed by company NAME. In
     * production every company must be in it — any company missing from
     * it, an invalid code or a duplicated one stops the migration with
     * the list, before anything is written. Elsewhere (dev, local,
     * tests), mapped names still get their fixed code and any other
     * company gets CompanyCode::suggest() (first three letters, next free
     * if taken); invalid or duplicate codes still stop it.
     *
     * NOT NULL is applied on MySQL/MariaDB only. On SQLite (the test
     * database) Laravel would apply ->change() by rebuilding both tables,
     * and dropping the old `organizations`/`tasks` table mid-rebuild fires
     * ON DELETE CASCADE on their children (see the add_upload_columns
     * migration). There the columns stay nullable; Organization::creating()
     * and TaskObserver::creating() fill them on every create.
     *
     * Public so the backfill tests can try other maps.
     *
     * @var array<string, string>
     */
    public array $codes = [
        'Aksara Virtual Agency' => 'AVA',
        'Bali Goods Sourcing' => 'BGS',
        'Bali Virtual Academy' => 'BVA',
        'English Hub' => 'EHU',
        'Solava' => 'SOL',
    ];

    /**
     * Safe to run again after a failure part-way: MySQL commits each
     * schema change on its own, so a step that fails (a lock timeout on
     * the NOT NULL change, say) would otherwise leave the earlier columns
     * behind and the next `migrate` dying on "Duplicate column". Every
     * step checks whether it has already happened, and the codes and
     * numbers are recomputed in full, the same way each time.
     */
    public function up(): void
    {
        // Every check runs before the first schema change, so a failure
        // leaves the database untouched.
        $codes = $this->resolveCodes();

        if (! Schema::hasColumn('organizations', 'code')) {
            Schema::table('organizations', function (Blueprint $table) {
                $table->char('code', 3)->nullable()->after('slug');
            });
        }
        if (! Schema::hasColumn('organizations', 'next_task_number')) {
            Schema::table('organizations', function (Blueprint $table) {
                $table->unsignedInteger('next_task_number')->default(1)->after('completed_window_days');
            });
        }

        foreach ($codes as $organizationId => $code) {
            DB::table('organizations')->where('id', $organizationId)->update(['code' => $code]);
        }

        if (! Schema::hasColumn('tasks', 'task_number')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->unsignedInteger('task_number')->nullable()->after('organization_id');
            });
        }

        $this->numberTasks();

        if (! $this->isSqlite()) {
            Schema::table('organizations', function (Blueprint $table) {
                $table->char('code', 3)->nullable(false)->change();
            });
            Schema::table('tasks', function (Blueprint $table) {
                $table->unsignedInteger('task_number')->nullable(false)->change();
            });
        }

        if (! Schema::hasIndex('organizations', ['code'], 'unique')) {
            Schema::table('organizations', function (Blueprint $table) {
                $table->unique('code');
            });
        }
        if (! Schema::hasIndex('tasks', ['organization_id', 'task_number'], 'unique')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->unique(['organization_id', 'task_number']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropUnique(['organization_id', 'task_number']);
        });
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('task_number');
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->dropUnique(['code']);
        });
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['code', 'next_task_number']);
        });
    }

    /**
     * Every company's code, deactivated ones included, or a
     * RuntimeException listing every problem at once.
     *
     * @return array<int, string> organization id => code
     */
    private function resolveCodes(): array
    {
        $organizations = DB::table('organizations')->orderBy('id')->get(['id', 'name', 'is_active']);
        $strict = app()->environment('production');
        $problems = [];

        foreach ($this->codes as $name => $code) {
            if (! CompanyCode::isValid($code)) {
                $problems[] = "the map's code '{$code}' for '{$name}' is not three uppercase letters";
            }
        }
        foreach (collect($this->codes)->countBy()->filter(fn (int $count) => $count > 1)->keys() as $code) {
            $problems[] = "the map gives '{$code}' to more than one company: ".collect($this->codes)->filter(fn ($mapped) => $mapped === $code)->keys()->map(fn ($name) => "'{$name}'")->implode(', ');
        }

        $unmapped = $organizations->reject(fn ($organization) => array_key_exists($organization->name, $this->codes));
        if ($strict && $unmapped->isNotEmpty()) {
            $problems[] = 'these companies have no code in the map: '.$unmapped
                ->map(fn ($organization) => "'{$organization->name}' (id {$organization->id}".($organization->is_active ? '' : ', deactivated').')')
                ->implode(', ');
        }

        if ($problems) {
            throw new RuntimeException('Task codes migration stopped: '.implode('; ', $problems).'.');
        }

        // Fixed codes first, so a suggested code can't take one of them.
        $codes = [];
        foreach ($organizations as $organization) {
            if (array_key_exists($organization->name, $this->codes)) {
                $codes[$organization->id] = $this->codes[$organization->name];
            }
        }
        foreach ($unmapped as $organization) {
            $codes[$organization->id] = CompanyCode::suggest($organization->name, array_merge(array_values($codes), array_values($this->codes)));
        }

        $duplicates = collect($codes)->countBy()->filter(fn (int $count) => $count > 1)->keys();
        if ($duplicates->isNotEmpty()) {
            throw new RuntimeException('Task codes migration stopped: these codes would be shared by more than one company: '.$duplicates
                ->map(fn (string $code) => $code.' ('.$organizations->whereIn('id', array_keys($codes, $code, true))->pluck('name')->map(fn ($name) => "'{$name}'")->implode(', ').')')
                ->implode('; ').'.');
        }

        return $codes;
    }

    /**
     * Each company's tasks numbered 1..N in id order, soft-deleted ones
     * included, in chunks of 500 in PHP (no window functions), one
     * UPDATE per chunk, then each counter set to max + 1 (1 for a
     * company with no tasks).
     */
    private function numberTasks(): void
    {
        foreach (DB::table('organizations')->orderBy('id')->pluck('id') as $organizationId) {
            $number = 0;

            DB::table('tasks')
                ->where('organization_id', $organizationId)
                ->select('id')
                ->chunkById(500, function ($tasks) use (&$number) {
                    // CASE id WHEN 41 THEN 1 WHEN 57 THEN 2 ... END: the ids
                    // and numbers are integers from this loop, cast again
                    // before they go into the SQL.
                    $cases = '';
                    foreach ($tasks as $task) {
                        $cases .= ' WHEN '.(int) $task->id.' THEN '.++$number;
                    }

                    DB::table('tasks')
                        ->whereIn('id', $tasks->pluck('id'))
                        ->update(['task_number' => DB::raw("CASE id{$cases} END")]);
                });

            DB::table('organizations')->where('id', $organizationId)->update(['next_task_number' => $number + 1]);
        }
    }

    private function isSqlite(): bool
    {
        return Schema::getConnection()->getDriverName() === 'sqlite';
    }
};
