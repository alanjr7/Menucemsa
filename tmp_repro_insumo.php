<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\AlmacenStock;

$user = User::where('role', 'emergencia')->orWhere('role', 'enfermera-emergencia')->first();
echo 'user=' . ($user ? $user->id : 'none') . ' role=' . ($user ? $user->role : 'none') . "\n";

if ($user) {
    $stocks = AlmacenStock::where('ubicacion', 'emergencia')
        ->where('cantidad_actual', '>', 0)
        ->whereHas('lote.catalogo', function ($q) {
            $q->where('tipo', 'insumo')
              ->where('activo', true)
              ->where('nombre', 'like', '%al%');
        })
        ->with('lote.catalogo')
        ->limit(5)
        ->get();
    echo 'count=' . $stocks->count() . "\n";
}
