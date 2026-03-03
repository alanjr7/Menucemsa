<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetalleRecetaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ID_FARMACIA' => $this->ID_FARMACIA,
            'CODIGO_MEDICAMENTOS' => $this->CODIGO_MEDICAMENTOS,
            'DOSIS' => $this->DOSIS,
            'SUBTOTAL' => (float) $this->SUBTOTAL,
            'farmacia' => new FarmaciaResource($this->whenLoaded('farmacia')),
            'medicamento' => new MedicamentosResource($this->whenLoaded('medicamento')),
        ];
    }
}
