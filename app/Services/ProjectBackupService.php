<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class ProjectBackupService
{
    public function enabled(): bool
    {
        return (bool) config('project_backup.enabled');
    }

    public function backupPath(): string
    {
        return rtrim((string) config('project_backup.path'), '/\\');
    }

    public function ensurePath(): string
    {
        $path = $this->backupPath();
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }
        return $path;
    }

    /**
     * Create backup zip. Returns array with status info.
     */
    public function create(): array
    {
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit', '512M');

        $path = $this->ensurePath();
        $ts = now()->format('Y-m-d_His');
        $dbName = (string) config('database.connections.' . config('project_backup.database_connection') . '.database', 'database');
        $sqlFile = $path . DIRECTORY_SEPARATOR . $dbName . '_' . $ts . '.sql';
        $zipFile = $path . DIRECTORY_SEPARATOR . 'backup-' . $ts . '.zip';

        $result = [
            'ok' => false,
            'zip' => $zipFile,
            'sql' => $sqlFile,
            'db_dump' => false,
            'files_added' => 0,
            'error' => null,
        ];

        try {
            $result['db_dump'] = $this->dumpDatabase($sqlFile);

            if (!class_exists(\ZipArchive::class)) {
                throw new \RuntimeException('ZipArchive extension غير مفعلة على السيرفر.');
            }

            $zip = new \ZipArchive();
            if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException('تعذر إنشاء ملف النسخة الاحتياطية.');
            }

            // add sql dump
            if (is_file($sqlFile)) {
                $zip->addFile($sqlFile, 'database/' . basename($sqlFile));
            }

            // add project files
            $root = base_path();
            $exclusions = array_map(fn($e) => str_replace(['/', '\\'], DIRECTORY_SEPARATOR, trim($e)), (array) config('project_backup.exclusions', []));
            $filesAdded = 0;
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );
            foreach ($iterator as $file) {
                /** @var \SplFileInfo $file */
                if ($file->isLink()) continue;
                if (!$file->isFile()) continue;
                $full = $file->getPathname();
                $rel = ltrim(str_replace($root, '', $full), '/\\');
                $relNorm = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $rel);
                if ($this->isExcluded($relNorm, $exclusions)) continue;
                // skip the backup output dir itself if inside project
                if (str_starts_with(strtolower($full), strtolower($path . DIRECTORY_SEPARATOR))) continue;
                // skip .env? prompt said "إذا ZIP سيشمل .env" be careful - include by default? better exclude .env for safety? prompt said "انتبه: لو ZIP سيشمل .env..." so be aware. I'll exclude .env to be safe.
                if (in_array(basename($relNorm), ['.env', '.env.backup', '.env.production'])) continue;
                $zip->addFile($full, 'project/' . str_replace(DIRECTORY_SEPARATOR, '/', $relNorm));
                $filesAdded++;
            }
            $zip->close();
            $result['files_added'] = $filesAdded;
            $result['ok'] = is_file($zipFile);

            // remove raw sql after zipping
            if (is_file($sqlFile)) @unlink($sqlFile);

            $this->prune();
        } catch (\Throwable $e) {
            $result['error'] = $e->getMessage();
            Log::error('Project backup failed: ' . $e->getMessage(), ['exception' => $e]);
            if (is_file($sqlFile)) @unlink($sqlFile);
        }

        return $result;
    }

    private function dumpDatabase(string $sqlFile): bool
    {
        $conn = config('project_backup.database_connection', 'mysql');
        $cfg = config('database.connections.' . $conn, []);
        if (($cfg['driver'] ?? '') !== 'mysql') {
            Log::warning('Project backup: DB dump skipped, driver not mysql');
            return false;
        }

        $mysqldump = (string) config('project_backup.mysqldump_path', 'mysqldump');
        $host = $cfg['host'] ?? '127.0.0.1';
        $port = (string) ($cfg['port'] ?? 3306);
        $db = $cfg['database'] ?? '';
        $user = $cfg['username'] ?? '';
        $pass = $cfg['password'] ?? '';

        $cmd = [
            $mysqldump,
            '--host=' . $host,
            '--port=' . $port,
            '--user=' . $user,
            '--single-transaction',
            '--routines',
            '--triggers',
            '--quick',
            '--result-file=' . $sqlFile,
            $db,
        ];

        $env = null;
        if ($pass !== '') {
            $env = array_merge($_SERVER, $_ENV, ['MYSQL_PWD' => $pass]);
        }

        try {
            $process = new Process($cmd, base_path(), $env, null, 300);
            $process->run();
            if (!$process->isSuccessful()) {
                Log::warning('mysqldump failed', ['err' => $process->getErrorOutput()]);
                return false;
            }
            return is_file($sqlFile) && filesize($sqlFile) > 0;
        } catch (\Throwable $e) {
            Log::warning('mysqldump exception: ' . $e->getMessage());
            return false;
        }
    }

    private function isExcluded(string $relNorm, array $exclusions): bool
    {
        foreach ($exclusions as $ex) {
            $ex = trim($ex, DIRECTORY_SEPARATOR);
            if ($ex === '') continue;
            if ($relNorm === $ex) return true;
            if (str_starts_with($relNorm, $ex . DIRECTORY_SEPARATOR)) return true;
        }
        return false;
    }

    public function prune(): int
    {
        $path = $this->backupPath();
        if (!File::isDirectory($path)) return 0;
        $days = max(1, (int) config('project_backup.retention_days', 14));
        $cutoff = now()->subDays($days)->getTimestamp();
        $deleted = 0;
        foreach (File::files($path) as $file) {
            if (!str_starts_with($file->getFilename(), 'backup-')) continue;
            if ($file->getMTime() < $cutoff) {
                @unlink($file->getPathname());
                $deleted++;
            }
        }
        return $deleted;
    }

    public function listBackups(): array
    {
        $path = $this->backupPath();
        if (!File::isDirectory($path)) return [];
        $files = collect(File::files($path))
            ->filter(fn($f) => str_starts_with($f->getFilename(), 'backup-') && str_ends_with($f->getFilename(), '.zip'))
            ->sortByDesc(fn($f) => $f->getMTime())
            ->map(fn($f) => [
                'name' => $f->getFilename(),
                'size' => $f->getSize(),
                'mtime' => $f->getMTime(),
                'date' => date('Y-m-d H:i:s', $f->getMTime()),
            ])->values()->all();
        return $files;
    }

    public function latestBackup(): ?array
    {
        $all = $this->listBackups();
        return $all[0] ?? null;
    }

    public function deleteBackup(string $filename): bool
    {
        if (!preg_match('/^backup-[\w\-]+\.zip$/', $filename)) return false;
        $full = $this->backupPath() . DIRECTORY_SEPARATOR . $filename;
        if (!is_file($full)) return false;
        return @unlink($full);
    }
}
