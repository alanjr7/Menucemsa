<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing route access...\n";

$user = \App\Models\User::where('role', 'emergencia')->first();
echo 'User found: ' . $user->email . "\n";

// Simulate authentication
auth()->login($user);

try {
    $controller = app('App\Http\Controllers\PatientsController');
    $response = $controller->index(request());
    echo "Controller method executed successfully\n";
    echo "Response type: " . get_class($response) . "\n";
} catch (Exception $e) {
    echo "Error in controller: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
