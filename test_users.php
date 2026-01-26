<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$users = \App\Models\User::all();
echo "Total Users: " . $users->count() . "\n\n";
foreach($users as $user) {
    echo "Username: " . $user->username . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Password Hash: " . substr($user->password, 0, 20) . "...\n";
    echo "Active: " . ($user->active ? 'Yes' : 'No') . "\n";
    echo "---\n";
}
