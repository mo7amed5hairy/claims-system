<?php
/**
 * Complete Login System Diagnostic
 */
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║         LOGIN SYSTEM DIAGNOSTIC REPORT                         ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// 1. Check Users in Database
echo "1️⃣  DATABASE USERS:\n";
echo str_repeat("─", 60) . "\n";
$users = \App\Models\User::all();
if ($users->count() > 0) {
    echo "✅ Users found: " . $users->count() . "\n\n";
    foreach ($users as $user) {
        echo "   • " . $user->username . " (" . $user->email . ")\n";
        echo "     Status: " . ($user->active ? "Active" : "Inactive") . "\n";
    }
} else {
    echo "❌ No users found in database!\n";
}
echo "\n";

// 2. Check Configuration
echo "2️⃣  CONFIGURATION:\n";
echo str_repeat("─", 60) . "\n";
echo "✅ APP_ENV: " . env('APP_ENV') . "\n";
echo "✅ SESSION_DRIVER: " . env('SESSION_DRIVER') . "\n";
echo "✅ CACHE_STORE: " . env('CACHE_STORE') . "\n";
echo "✅ DB_DATABASE: " . env('DB_DATABASE') . "\n";
echo "\n";

// 3. Check Routes
echo "3️⃣  ROUTES:\n";
echo str_repeat("─", 60) . "\n";

use Illuminate\Support\Facades\Route;
$routes = Route::getRoutes();
$loginRoutes = [];
foreach ($routes as $route) {
    if (strpos($route->uri, 'login') !== false || $route->uri === '/') {
        $loginRoutes[] = $route;
    }
}

if (!empty($loginRoutes)) {
    echo "✅ Login routes found:\n";
    foreach ($loginRoutes as $route) {
        $methods = implode('|', $route->methods);
        echo "   • " . $methods . " " . $route->uri;
        if ($route->getName()) {
            echo " (" . $route->getName() . ")";
        }
        echo "\n";
    }
} else {
    echo "❌ No login routes found!\n";
}
echo "\n";

// 4. Test Authentication
echo "4️⃣  AUTHENTICATION TEST:\n";
echo str_repeat("─", 60) . "\n";

use Illuminate\Support\Facades\Auth;

$testUser = \App\Models\User::where('username', 'admin')->first();
if ($testUser) {
    echo "Testing user: " . $testUser->username . "\n\n";
    
    if (Auth::guard('web')->attempt(['username' => 'admin', 'password' => 'password'])) {
        echo "✅ Authentication SUCCESS\n";
        echo "   User logged in: " . Auth::guard('web')->user()->username . "\n";
        Auth::guard('web')->logout();
    } else {
        echo "❌ Authentication FAILED\n";
        echo "   Password verification failed or user credentials incorrect\n";
    }
} else {
    echo "❌ Test user 'admin' not found\n";
}
echo "\n";

// 5. Check Session Directory
echo "5️⃣  SESSION DIRECTORY:\n";
echo str_repeat("─", 60) . "\n";
$sessionPath = storage_path('framework/sessions');
if (is_dir($sessionPath)) {
    echo "✅ Session directory exists: " . $sessionPath . "\n";
    echo "   Permissions: " . substr(sprintf('%o', fileperms($sessionPath)), -4) . "\n";
} else {
    echo "⚠️  Session directory does not exist\n";
}
echo "\n";

// 6. Check Log File
echo "6️⃣  LOG FILE:\n";
echo str_repeat("─", 60) . "\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    echo "✅ Log file exists: " . $logFile . "\n";
    echo "   Size: " . filesize($logFile) . " bytes\n";
    echo "   Last modified: " . date('Y-m-d H:i:s', filemtime($logFile)) . "\n";
} else {
    echo "⚠️  Log file does not exist yet\n";
}
echo "\n";

// 7. Summary
echo "7️⃣  SUMMARY:\n";
echo str_repeat("─", 60) . "\n";
$allOk = $users->count() > 0 && !empty($loginRoutes) && $testUser;
if ($allOk) {
    echo "✅ ALL SYSTEMS GO! The login system is ready to use.\n";
} else {
    echo "⚠️  Some issues detected. Check the output above.\n";
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                    END OF REPORT                               ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";
