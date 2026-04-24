<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$request = new \Illuminate\Http\Request();
$request->merge(['fecha_inicio' => '2026-04-24', 'fecha_fin' => '2026-04-24', 'estado' => 'todas']);
$controller = new \App\Http\Controllers\Caja\CajaGestionController();
$response = $controller->getControlCajas($request);
echo $response->getContent();
