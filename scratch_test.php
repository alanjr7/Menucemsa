<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = App\Services\CuentaCobroService::crearCuentaInternacion('55555258', 'TEST-123'); 
echo json_encode($c);
