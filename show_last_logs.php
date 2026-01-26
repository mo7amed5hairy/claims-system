<?php
/**
 * Show Last Login Logs
 * عرض آخر 30 سطر من الـ logs
 */

$logFile = __DIR__ . '/storage/logs/laravel.log';

if (!file_exists($logFile)) {
    die("❌ Log file not found!\n");
}

$lines = file($logFile);
$lastLines = array_slice($lines, -30);

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                   LAST 30 LOG LINES                            ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

foreach ($lastLines as $line) {
    // Color code the output
    if (strpos($line, 'SUCCESS') !== false || strpos($line, '✅') !== false) {
        echo "\033[32m" . $line . "\033[0m"; // Green
    } elseif (strpos($line, 'FAILED') !== false || strpos($line, '❌') !== false || strpos($line, 'ERROR') !== false) {
        echo "\033[31m" . $line . "\033[0m"; // Red
    } elseif (strpos($line, 'LOGIN') !== false || strpos($line, '===') !== false) {
        echo "\033[33m" . $line . "\033[0m"; // Yellow
    } else {
        echo $line;
    }
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║              If you see ❌ FAILED, the problem is:             ║\n";
echo "║              - Wrong password                                  ║\n";
echo "║              - User not found in database                      ║\n";
echo "║                                                                ║\n";
echo "║              If you see ✅ SUCCESS but still no login:         ║\n";
echo "║              - Session problem                                 ║\n";
echo "║              - Redirect problem                                ║\n";
echo "║              - Middleware problem                              ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";
