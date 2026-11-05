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
 * Fails, keeping no file, if the dump exits non-zero, writes nothing,
 * doesn't end with its "-- Dump completed" line (a dump cut short), or a
 * write fails (a full disk or quota). Before the file is kept it is read
 * back: it must decompress to every byte the dump produced and end with
 * that line. Only then are the oldest backups beyond --keep deleted, so a
 * bad backup never replaces good ones.
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
            $bytes = $this->dump($config, $partial);
            $this->verify($partial, $bytes);
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
     * in memory or writing it uncompressed. Returns how many bytes the
     * dump produced, for verify().
     *
     * @param  array<string, mixed>  $config
     */
    private function dump(array $config, string $partial): int
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
        // Set rather than thrown inside the output callback, so the dump
        // still runs to its end instead of being left half-read.
        $writeFailed = false;

        try {
            $result = Process::env(['MYSQL_PWD' => (string) ($config['password'] ?? '')])
                ->timeout(1800)
                ->start($this->dumpCommand($config), function (string $type, string $output) use ($gzip, &$bytes, &$tail, &$errors, &$writeFailed) {
                    if ($type === 'out') {
                        if (! $writeFailed && $this->writeChunk($gzip, $output) !== strlen($output)) {
                            $writeFailed = true;
                        }
                        $bytes += strlen($output);
                        $tail = substr($tail.$output, -512);
                    } else {
                        $errors = substr($errors.$output, -2000);
                    }
                })
                ->wait();
        } finally {
            $closed = gzclose($gzip);
        }

        if (! $result->successful()) {
            throw new RuntimeException('the dump exited with code '.$result->exitCode().'. '.trim($errors));
        }
        if ($bytes === 0) {
            throw new RuntimeException('the dump wrote nothing. '.trim($errors));
        }

        if (! $this->endsCompleted($tail)) {
            throw new RuntimeException('the dump did not finish (its last line is not "'.self::COMPLETED_MARKER.'"). '.trim($errors));
        }
        if ($writeFailed || ! $closed) {
            throw new RuntimeException('writing the backup file failed (is the disk or quota full?).');
        }

        return $bytes;
    }

    /** Whether the dump's last line is the "-- Dump completed" one. */
    private function endsCompleted(string $tail): bool
    {
        $lines = preg_split('/\R/', rtrim($tail));

        return str_starts_with((string) end($lines), self::COMPLETED_MARKER);
    }

    /** One chunk of the dump into the gzip file; the bytes written, or false. */
    protected function writeChunk($gzip, string $data): int|false
    {
        return @gzwrite($gzip, $data);
    }

    /**
     * Reads the finished file back: it must decompress to exactly the
     * $bytes the dump produced and end with the "-- Dump completed" line.
     */
    private function verify(string $partial, int $bytes): void
    {
        $gzip = @gzopen($partial, 'rb');
        if ($gzip === false) {
            throw new RuntimeException("can't read the backup file back.");
        }

        $read = 0;
        $tail = '';

        try {
            while (! gzeof($gzip)) {
                $chunk = @gzread($gzip, 1024 * 1024);
                if ($chunk === false) {
                    throw new RuntimeException('the backup file is corrupt (it does not decompress).');
                }
                if ($chunk === '') {
                    break;
                }
                $read += strlen($chunk);
                $tail = substr($tail.$chunk, -512);
            }
        } finally {
            gzclose($gzip);
        }

        if ($read !== $bytes || ! $this->endsCompleted($tail)) {
            throw new RuntimeException("the backup file doesn't match the dump when read back ({$read} of {$bytes} bytes).");
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
            // The connection's own charset, so utf8mb4 text (emoji in a
            // title or comment) survives the round trip.
            '--default-character-set='.(filled($config['charset'] ?? null) ? $config['charset'] : 'utf8mb4'),
        ];

        // Empty values are left out (the client's defaults apply) rather
        // than passed as "--port=".
        if (filled($config['unix_socket'] ?? null)) {
            $command[] = '--socket='.$config['unix_socket'];
        } else {
            if (filled($config['host'] ?? null)) {
                $command[] = '--host='.$config['host'];
            }
            if (filled($config['port'] ?? null)) {
                $command[] = '--port='.$config['port'];
            }
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
