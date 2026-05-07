<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing full route access...\n";

$user = \App\Models\User::where('role', 'emergencia')->first();
echo 'User found: ' . $user->email . "\n";
echo 'User role: ' . $user->role . "\n";

// Create a request
$request = \Illuminate\Http\Request::create('/patients', 'GET');

// Simulate authentication
auth()->login($user);

echo "Authenticated user ID: " . auth()->id() . "\n";

try {
    // Test route resolution
    $route = \Illuminate\Support\Facades\Route::getRoutes()->match($request);
    echo "Route found: " . $route->getName() . "\n";
    
    // Test middleware
    $middleware = $route->gatherMiddleware();
    echo "Middleware: " . implode(', ', $middleware) . "\n";
    
    // Test dispatching through router
    $response = $kernel->handle($request);
    echo "Response status: " . $response->getStatusCode() . "\n";
    
    if ($response->getStatusCode() === 403) {
        echo "403 Forbidden detected!\n";
        echo "Response content: " . $response->getContent() . "\n";
    } else {
        echo "Route accessed successfully!\n";
    }
    
    $kernel->terminate($request, $response);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
