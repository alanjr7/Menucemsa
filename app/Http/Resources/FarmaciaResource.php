<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FarmaciaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ID' => $this->ID,
            'DETALLE' => $this->DETALLE,
            'detalle_medicamentos' => DetalleMedicamentosResource::collection($this->whenLoaded('detalleMedicamentos')),
            'detalle_insumos' => DetalleInsumosResource::collection($this->whenLoaded('detalleInsumos')),
            'detalle_recetas' => DetalleRecetaResource::collection($this->whenLoaded('detalleRecetas')),
            'inventarios' => InventarioResource::collection($this->whenLoaded('inventarios')),
        ];
    }
}
