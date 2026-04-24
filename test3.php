<?php
require 'vendor/autoload.php';
\ = require_once 'bootstrap/app.php';
\ = \->make(Illuminate\\Contracts\\Console\\Kernel::class);
\->bootstrap();
\ = new \\Illuminate\\Http\\Request();
\->merge(['fecha_inicio' => '2026-04-24', 'fecha_fin' => '2026-04-24', 'estado' => 'todas']);
\ = new \\App\\Http\\Controllers\\Caja\\CajaGestionController();
\ = \->getControlCajas(\);
echo \->getContent();
