<?php
/**
 * Real-time Log Viewer
 * تابع الـ logs أثناء محاولة اللوجن
 */

$logFile = __DIR__ . '/storage/logs/laravel.log';

// مسح الملف الحالي (اختياري)
echo "Opening log file: " . $logFile . "\n\n";

// إذا كنت تريد مسح الـ logs القديمة
echo "Do you want to clear old logs? (watching will start fresh)\n";
echo "Current log size: " . (file_exists($logFile) ? filesize($logFile) : 0) . " bytes\n\n";

// قراءة آخر 100 سطر من الـ log
if (file_exists($logFile)) {
    $lines = file($logFile);
    $lastLines = array_slice($lines, -100);
    
    echo "╔════════════════════════════════════════════════════════════════╗\n";
    echo "║                     LAST 100 LOG LINES                         ║\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n\n";
    
    foreach ($lastLines as $line) {
        echo $line;
    }
    
    echo "\n\n";
    echo "╔════════════════════════════════════════════════════════════════╗\n";
    echo "║          Now try logging in and refresh this page              ║\n";
    echo "║                 Logs will appear above ☝️                       ║\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n";
} else {
    echo "❌ Log file not found: " . $logFile . "\n";
    echo "Run: php artisan serve\n";
    echo "Then try logging in\n";
}
