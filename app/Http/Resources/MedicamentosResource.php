<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicamentosResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'CODIGO' => $this->CODIGO,
            'DESCRIPCION' => $this->DESCRIPCION,
            'PRECIO' => (float) $this->PRECIO,
            'detalle_medicamentos' => DetalleMedicamentosResource::collection($this->whenLoaded('detalleMedicamentos')),
            'detalle_recetas' => DetalleRecetaResource::collection($this->whenLoaded('detalleRecetas')),
        ];
    }
}
