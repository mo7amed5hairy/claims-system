#!/usr/bin/env php
<?php
/**
 * Monitor Login Logs in Real-time
 * تابع الـ logs أثناء اللوجن مباشرة
 */

$logFile = __DIR__ . '/storage/logs/laravel.log';

// تنظيف الـ logs السابقة
if (file_exists($logFile)) {
    file_put_contents($logFile, '');
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║              LOGIN LOG MONITORING STARTED                      ║\n";
echo "║                                                                ║\n";
echo "║  1. Keep this terminal open                                   ║\n";
echo "║  2. Go to http://localhost:8000                               ║\n";
echo "║  3. Try to login with: admin / password                       ║\n";
echo "║  4. Logs will appear below automatically                      ║\n";
echo "║                                                                ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$lastSize = 0;
$lastPosition = 0;

while (true) {
    if (file_exists($logFile)) {
        $currentSize = filesize($logFile);
        
        if ($currentSize > $lastPosition) {
            $handle = fopen($logFile, 'r');
            fseek($handle, $lastPosition);
            
            while (!feof($handle)) {
                $line = fgets($handle);
                if ($line !== false && trim($line) !== '') {
                    // Highlight important lines
                    if (strpos($line, 'SUCCESS') !== false) {
                        echo "\033[32m✅ " . $line . "\033[0m";
                    } elseif (strpos($line, 'FAILED') !== false || strpos($line, 'ERROR') !== false) {
                        echo "\033[31m❌ " . $line . "\033[0m";
                    } elseif (strpos($line, 'INFO') !== false) {
                        echo "\033[34mℹ️  " . $line . "\033[0m";
                    } else {
                        echo $line;
                    }
                }
            }
            
            $lastPosition = ftell($handle);
            fclose($handle);
        }
    }
    
    sleep(1); // Check every 1 second
}
?>
