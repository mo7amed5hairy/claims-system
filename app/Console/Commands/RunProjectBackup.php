<?php

namespace App\Console\Commands;

use App\Services\ProjectBackupService;
use Illuminate\Console\Command;

class RunProjectBackup extends Command
{
    protected $signature = 'backup:project';
    protected $description = 'Create project + database backup ZIP';

    public function handle(ProjectBackupService $service): int
    {
        if (!$service->enabled()) {
            $this->warn('Project backup is disabled (PROJECT_BACKUP_ENABLED=false).');
            return self::SUCCESS;
        }

        $this->info('Creating backup...');
        $result = $service->create();
        if ($result['ok']) {
            $this->info('Backup created: ' . $result['zip']);
            $this->info('DB dump: ' . ($result['db_dump'] ? 'yes' : 'no'));
            $this->info('Files: ' . $result['files_added']);
            return self::SUCCESS;
        }

        $this->error('Backup failed: ' . ($result['error'] ?? 'unknown'));
        return self::FAILURE;
    }
}