<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetalleMedicamentosResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ID_FARMACIA' => $this->ID_FARMACIA,
            'CODIGO_MEDICAMENTOS' => $this->CODIGO_MEDICAMENTOS,
            'LABORATORIO' => $this->LABORATORIO,
            'FECHA_VENCIMIENTO' => $this->FECHA_VENCIMIENTO,
            'TIPO' => $this->TIPO,
            'REQUERIMIENTO' => $this->REQUERIMIENTO,
            'farmacia' => new FarmaciaResource($this->whenLoaded('farmacia')),
            'medicamento' => new MedicamentosResource($this->whenLoaded('medicamento')),
        ];
    }
}
