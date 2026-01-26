<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;

// Test authentication
$user = \App\Models\User::where('username', 'admin')->first();

if ($user) {
    echo "✅ User found: " . $user->username . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Password hash exists: " . (strlen($user->password) > 0 ? "Yes" : "No") . "\n\n";
    
    // Test password verification
    if (Auth::guard('web')->attempt(['username' => 'admin', 'password' => 'password'])) {
        echo "✅ Authentication works! User logged in.\n";
        echo "Authenticated user: " . Auth::guard('web')->user()->username . "\n";
    } else {
        echo "❌ Authentication failed - password mismatch\n";
    }
} else {
    echo "❌ User not found\n";
}
