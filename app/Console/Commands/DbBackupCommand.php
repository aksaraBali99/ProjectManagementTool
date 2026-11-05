<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Number;
use RuntimeException;
use Symfony\Component\Process\ExecutableFinder;
use Throwable;

/**
 * task #119: a gzipped dump of the default database connection, taken by
 * deploy.sh --backup right before every PROD deploy changes anything.
 *
 * MySQL and MariaDB only (PROD runs MariaDB 11.8; DB_CONNECTION may say
 * mysql or mariadb). Uses mariadb-dump when the server has it, else
 * mysqldump (MariaDB 11's mysqldump name still works, but warns that it
 * is deprecated).
 *
 * Writes storage/app/backups/db-YYYYmmdd-HHMMSS.sql.gz: outside public/,
 * the folder 700 and each file 600, since a dump holds all user data. The
 * password goes to the dump through MYSQL_PWD, never as an argument (the
 * process list would show it), and nothing here prints it.
 *
 * Fails, keeping no file, if the dump exits non-zero, writes nothing, or
 * doesn't end with its "-- Dump completed" line (a dump cut short). Then
 * deletes the oldest backups beyond --keep.
 */
class DbBackupCommand extends Command
{
    protected $signature = 'solva:db-backup {--keep=5 : How many backups to keep, newest first}';

    protected $description = 'Back up the MySQL/MariaDB database to storage/app/backups (gzipped).';

    /** The dump's last line when it finished. */
    private const COMPLETED_MARKER = '-- Dump completed';

    public function handle(): int
    {
        $keep = filter_var($this->option('keep'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($keep === false) {
            $this->error('--keep must be a whole number, 1 or more.');

            return self::FAILURE;
        }

        $connection = config('database.default');
        $config = config("database.connections.{$connection}", []);
        $driver = $config['driver'] ?? 'unknown';

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            $this->error("Backups support the mysql and mariadb drivers only; the default connection '{$connection}' uses '{$driver}'.");

            return self::FAILURE;
        }

        $directory = storage_path('app/backups');
        File::ensureDirectoryExists($directory, 0700);
        @chmod($directory, 0700);

        $path = $directory.'/db-'.now()->format('Ymd-His').'.sql.gz';
        $partial = $path.'.partial';

        try {
            $this->dump($config, $partial);
        } catch (Throwable $e) {
            File::delete($partial);
            $this->error('Backup failed: '.$e->getMessage());

            return self::FAILURE;
        }

        File::move($partial, $path);
        $this->info('Backup written: '.basename($path).' ('.Number::fileSize(File::size($path), precision: 1).')');

        $this->prune($directory, $keep);

        return self::SUCCESS;
    }

    /**
     * Streams the dump straight into a gzip file, never holding it whole
     * in memory or writing it uncompressed.
     *
     * @param  array<string, mixed>  $config
     */
    private function dump(array $config, string $partial): void
    {
        touch($partial);
        @chmod($partial, 0600);

        $gzip = gzopen($partial, 'wb6');
        if ($gzip === false) {
            throw new RuntimeException("can't write {$partial}.");
        }

        $bytes = 0;
        $tail = '';
        $errors = '';

        try {
            $result = Process::env(['MYSQL_PWD' => (string) ($config['password'] ?? '')])
                ->timeout(1800)
                ->start($this->dumpCommand($config), function (string $type, string $output) use ($gzip, &$bytes, &$tail, &$errors) {
                    if ($type === 'out') {
                        gzwrite($gzip, $output);
                        $bytes += strlen($output);
                        $tail = substr($tail.$output, -512);
                    } else {
                        $errors = substr($errors.$output, -2000);
                    }
                })
                ->wait();
        } finally {
            gzclose($gzip);
        }

        if (! $result->successful()) {
            throw new RuntimeException('the dump exited with code '.$result->exitCode().'. '.trim($errors));
        }
        if ($bytes === 0) {
            throw new RuntimeException('the dump wrote nothing. '.trim($errors));
        }

        $lines = preg_split('/\R/', rtrim($tail));
        if (! str_starts_with((string) end($lines), self::COMPLETED_MARKER)) {
            throw new RuntimeException('the dump did not finish (its last line is not "'.self::COMPLETED_MARKER.'"). '.trim($errors));
        }
    }

    /**
     * @param  array<string, mixed>  $config
     * @return list<string>
     */
    private function dumpCommand(array $config): array
    {
        $command = [
            app(ExecutableFinder::class)->find('mariadb-dump') ?? 'mysqldump',
            '--single-transaction',
            '--quick',
            '--no-tablespaces',
        ];

        if (! empty($config['unix_socket'])) {
            $command[] = '--socket='.$config['unix_socket'];
        } else {
            $command[] = '--host='.$config['host'];
            $command[] = '--port='.$config['port'];
        }

        $command[] = '--user='.$config['username'];
        $command[] = $config['database'];

        return $command;
    }

    /** Deletes the oldest backups beyond $keep. The names sort by time. */
    private function prune(string $directory, int $keep): void
    {
        $backups = collect(File::glob($directory.'/db-*.sql.gz'))->sort()->values();

        foreach ($backups->slice(0, max(0, $backups->count() - $keep)) as $old) {
            File::delete($old);
            $this->line('Deleted old backup: '.basename($old));
        }
    }
}
