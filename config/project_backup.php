<?php

return [
    'enabled' => filter_var(env('PROJECT_BACKUP_ENABLED', false), FILTER_VALIDATE_BOOL),
    'path' => env('PROJECT_BACKUP_PATH', 'C:/Backups/claims-system'),
    'retention_days' => (int) env('PROJECT_BACKUP_RETENTION_DAYS', 14),
    'daily_at' => env('PROJECT_BACKUP_DAILY_AT', '02:30'),
    'mysqldump_path' => env('PROJECT_BACKUP_MYSQLDUMP', 'mysqldump'),
    'database_connection' => env('DB_CONNECTION', 'mysql'),
    'exclusions' => [
        'vendor',
        'node_modules',
        '.git',
        'storage/framework/cache',
        'storage/framework/sessions',
        'storage/framework/views',
        'storage/logs',
        'public/build',
    ],
];