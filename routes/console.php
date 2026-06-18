<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Respaldo automático: se evalúa cada minuto y el propio comando decide si
// corresponde generar el backup según la frecuencia/hora configuradas (así se
// respeta el minuto exacto elegido en la UI). withoutOverlapping evita corridas
// solapadas. En cPanel basta un único cron: php artisan schedule:run.
Schedule::command('backup:auto')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
