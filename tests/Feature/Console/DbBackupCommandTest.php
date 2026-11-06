<?php

use App\Console\Commands\DbBackupCommand;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Process\ExecutableFinder;

/**
 * task #119: `solva:db-backup`, the backup every PROD deploy takes before
 * it changes anything. mysqldump is faked; the files go to a throwaway
 * storage folder.
 */
beforeEach(function () {
    $this->storage = sys_get_temp_dir().'/solava-backup-test-'.uniqid();
    File::ensureDirectoryExists($this->storage.'/app');
    $this->app->useStoragePath($this->storage);
    $this->backups = $this->storage.'/app/backups';

    $this->testConnection = config('database.default');
    config([
        'database.default' => 'mysql',
        'database.connections.mysql.host' => 'db.internal',
        'database.connections.mysql.port' => '3307',
        'database.connections.mysql.database' => 'solava_prod',
        'database.connections.mysql.username' => 'solava_user',
        'database.connections.mysql.password' => 'S3cret-Pa55!',
        'database.connections.mysql.unix_socket' => '',
        'database.connections.mysql.charset' => 'utf8mb4',
    ]);

    // No mariadb-dump on this "server" unless a test says so.
    useDumpBinary(null);
    $this->travelTo(now()->setDate(2026, 11, 5)->setTime(14, 30, 15));
});

afterEach(function () {
    // Back to the test database before RefreshDatabase rolls back.
    config(['database.default' => $this->testConnection]);
    File::deleteDirectory($this->storage);
});

function useDumpBinary(?string $mariadbDump): void
{
    app()->instance(ExecutableFinder::class, new class($mariadbDump) extends ExecutableFinder
    {
        public function __construct(private ?string $mariadbDump) {}

        public function find(string $name, ?string $default = null, array $extraDirs = []): ?string
        {
            return $name === 'mariadb-dump' ? $this->mariadbDump : $default;
        }
    });
}

function completeDump(): string
{
    return "-- MariaDB dump 10.19\nCREATE TABLE `users` (`id` int);\nINSERT INTO `users` VALUES (1);\n-- Dump completed on 2026-11-05 14:30:16\n";
}

/** @return list<string> */
function backupFiles(string $directory): array
{
    return File::isDirectory($directory) ? collect(File::files($directory))->map->getFilename()->sort()->values()->all() : [];
}

test('it runs mysqldump with the expected arguments, the password only in MYSQL_PWD', function () {
    Process::fake(['*' => Process::result(output: completeDump())]);

    $this->artisan('solva:db-backup')->assertSuccessful();

    Process::assertRan(function (PendingProcess $process) {
        expect($process->command)->toBe([
            'mysqldump', '--single-transaction', '--quick', '--no-tablespaces', '--default-character-set=utf8mb4',
            '--host=db.internal', '--port=3307', '--user=solava_user', 'solava_prod',
        ])
            ->and($process->environment['MYSQL_PWD'] ?? null)->toBe('S3cret-Pa55!')
            ->and(implode(' ', $process->command))->not->toContain('S3cret');

        return true;
    });
});

test('it uses mariadb-dump when the server has it', function () {
    useDumpBinary('/usr/bin/mariadb-dump');
    Process::fake(['*' => Process::result(output: completeDump())]);

    $this->artisan('solva:db-backup')->assertSuccessful();

    Process::assertRan(fn (PendingProcess $process) => $process->command[0] === '/usr/bin/mariadb-dump');
});

test('a socket connection dumps through the socket', function () {
    config(['database.connections.mysql.unix_socket' => '/var/run/mysqld.sock']);
    Process::fake(['*' => Process::result(output: completeDump())]);

    $this->artisan('solva:db-backup')->assertSuccessful();

    Process::assertRan(fn (PendingProcess $process) => in_array('--socket=/var/run/mysqld.sock', $process->command, true)
        && ! collect($process->command)->contains(fn ($argument) => str_starts_with($argument, '--host=')));
});

test('the dump uses the connection\'s charset, utf8mb4 when it has none', function (?string $charset, string $expected) {
    config(['database.connections.mysql.charset' => $charset]);
    Process::fake(['*' => Process::result(output: completeDump())]);

    $this->artisan('solva:db-backup')->assertSuccessful();

    Process::assertRan(fn (PendingProcess $process) => in_array("--default-character-set={$expected}", $process->command, true));
})->with([
    'from the config' => ['latin1', 'latin1'],
    'empty' => ['', 'utf8mb4'],
    'missing' => [null, 'utf8mb4'],
]);

test('an empty host or port is left out, not passed as "--port="', function () {
    config(['database.connections.mysql.host' => '', 'database.connections.mysql.port' => null]);
    Process::fake(['*' => Process::result(output: completeDump())]);

    $this->artisan('solva:db-backup')->assertSuccessful();

    Process::assertRan(fn (PendingProcess $process) => ! collect($process->command)
        ->contains(fn ($argument) => str_starts_with($argument, '--host') || str_starts_with($argument, '--port')));
});

test('it writes the dump gzipped under storage/app/backups, and prints its name and size but no credentials', function () {
    Process::fake(['*' => Process::result(output: completeDump())]);

    $this->artisan('solva:db-backup')
        ->expectsOutputToContain('Backup written: db-20261105-143015.sql.gz (')
        ->doesntExpectOutputToContain('S3cret-Pa55!')
        ->assertSuccessful();

    expect(backupFiles($this->backups))->toBe(['db-20261105-143015.sql.gz'])
        ->and(gzdecode(File::get($this->backups.'/db-20261105-143015.sql.gz')))->toBe(completeDump());
});

test('the backups folder is 700 and each file 600', function () {
    Process::fake(['*' => Process::result(output: completeDump())]);

    $this->artisan('solva:db-backup')->assertSuccessful();

    expect(fileperms($this->backups) & 0777)->toBe(0700)
        ->and(fileperms($this->backups.'/db-20261105-143015.sql.gz') & 0777)->toBe(0600);
})->skip(PHP_OS_FAMILY === 'Windows', 'Windows has no Unix file modes; CI (Linux) checks this.');

test('it keeps only the newest --keep backups, deleting the oldest', function () {
    File::ensureDirectoryExists($this->backups);
    foreach (['db-20261101-010000', 'db-20261102-010000', 'db-20261103-010000', 'db-20261104-010000'] as $old) {
        File::put("{$this->backups}/{$old}.sql.gz", 'old');
    }
    // Not a backup: never touched.
    File::put("{$this->backups}/notes.txt", 'keep me');
    Process::fake(['*' => Process::result(output: completeDump())]);

    $this->artisan('solva:db-backup', ['--keep' => 3])
        ->expectsOutputToContain('Deleted old backup: db-20261101-010000.sql.gz')
        ->assertSuccessful();

    expect(backupFiles($this->backups))->toBe([
        'db-20261103-010000.sql.gz', 'db-20261104-010000.sql.gz', 'db-20261105-143015.sql.gz', 'notes.txt',
    ]);
});

test('it keeps 5 by default', function () {
    File::ensureDirectoryExists($this->backups);
    foreach (range(1, 6) as $day) {
        File::put($this->backups.'/db-2026100'.$day.'-010000.sql.gz', 'old');
    }
    Process::fake(['*' => Process::result(output: completeDump())]);

    $this->artisan('solva:db-backup')->assertSuccessful();

    expect(backupFiles($this->backups))->toHaveCount(5)
        ->toContain('db-20261105-143015.sql.gz')
        ->not->toContain('db-20261002-010000.sql.gz');
});

test('it fails and keeps no file when the dump fails, is empty or was cut short', function (Closure $result, string $message) {
    File::ensureDirectoryExists($this->backups);
    File::put("{$this->backups}/db-20261101-010000.sql.gz", 'old');
    Process::fake(['*' => $result()]);

    $this->artisan('solva:db-backup', ['--keep' => 1])
        ->expectsOutputToContain($message)
        ->assertFailed();

    // Nothing new kept, no partial file left, and a failure never prunes.
    expect(backupFiles($this->backups))->toBe(['db-20261101-010000.sql.gz']);
})->with([
    'non-zero exit' => [fn () => Process::result(output: '', errorOutput: "mysqldump: Got error: 1045: Access denied for user 'solava_user'", exitCode: 2), 'exited with code 2'],
    'no output' => [fn () => Process::result(output: ''), 'the dump wrote nothing'],
    'cut short' => [fn () => Process::result(output: "-- MariaDB dump 10.19\nINSERT INTO `users` VALUES (1);\n"), 'did not finish'],
]);

test('it accepts the mariadb driver', function () {
    config([
        'database.default' => 'mariadb',
        'database.connections.mariadb.host' => 'maria.internal',
        'database.connections.mariadb.port' => '3306',
        'database.connections.mariadb.database' => 'solava',
        'database.connections.mariadb.username' => 'maria_user',
        'database.connections.mariadb.password' => 'pw',
        'database.connections.mariadb.unix_socket' => '',
    ]);
    Process::fake(['*' => Process::result(output: completeDump())]);

    $this->artisan('solva:db-backup')->assertSuccessful();

    Process::assertRan(fn (PendingProcess $process) => in_array('--host=maria.internal', $process->command, true)
        && end($process->command) === 'solava');
});

test('it refuses any other driver without running anything', function () {
    config(['database.default' => 'sqlite']);
    Process::fake();

    $this->artisan('solva:db-backup')
        ->expectsOutputToContain("uses 'sqlite'")
        ->assertFailed();

    Process::assertNothingRan();
    expect(backupFiles($this->backups))->toBe([]);
});

test('--keep must be 1 or more', function (string $keep) {
    Process::fake();

    $this->artisan('solva:db-backup', ['--keep' => $keep])->assertFailed();

    Process::assertNothingRan();
})->with(['0', '-1', 'abc']);

/**
 * The command with its gzip writes replaced: $write gets the gzip handle
 * and one chunk, and returns what gzwrite() would.
 */
function useChunkWriter(Closure $write): void
{
    app()->bind(DbBackupCommand::class, fn () => new class($write) extends DbBackupCommand
    {
        public function __construct(private Closure $write)
        {
            parent::__construct();
        }

        protected function writeChunk($gzip, string $data): int|false
        {
            return ($this->write)($gzip, $data);
        }
    });
}

test('a failed write (a full disk) fails the backup, keeps no file and deletes no older backup', function () {
    File::ensureDirectoryExists($this->backups);
    File::put("{$this->backups}/db-20261101-010000.sql.gz", 'old');
    useChunkWriter(fn ($gzip, string $data) => false);
    Process::fake(['*' => Process::result(output: completeDump())]);

    $this->artisan('solva:db-backup', ['--keep' => 1])
        ->expectsOutputToContain('writing the backup file failed')
        ->doesntExpectOutputToContain('Backup written')
        ->assertFailed();

    expect(backupFiles($this->backups))->toBe(['db-20261101-010000.sql.gz']);
});

test('a file that doesn\'t read back as the whole dump fails the backup, keeps no file and deletes no older backup', function () {
    File::ensureDirectoryExists($this->backups);
    File::put("{$this->backups}/db-20261101-010000.sql.gz", 'old');
    // Writes only half of each chunk but reports success, the way a lost
    // write would look: only reading the file back catches it.
    useChunkWriter(function ($gzip, string $data) {
        gzwrite($gzip, substr($data, 0, intdiv(strlen($data), 2)));

        return strlen($data);
    });
    Process::fake(['*' => Process::result(output: completeDump())]);

    $this->artisan('solva:db-backup', ['--keep' => 1])
        ->expectsOutputToContain("doesn't match the dump when read back")
        ->assertFailed();

    expect(backupFiles($this->backups))->toBe(['db-20261101-010000.sql.gz']);
});
