<?php

namespace App\Exports;

use App\Models\Episodio;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EpisodioExport implements WithMultipleSheets
{
    public function __construct(protected Episodio $episodio) {}

    public function sheets(): array
    {
        return [
            new EpisodioResumenSheet($this->episodio),
            new EpisodioEvaluacionesSheet($this->episodio),
            new EpisodioContableSheet($this->episodio),
        ];
    }
}
