<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InsumosResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'CODIGO' => $this->CODIGO,
            'NOMBRE' => $this->NOMBRE,
            'DESCRIPCION' => $this->DESCRIPCION,
            'detalle_insumos' => DetalleInsumosResource::collection($this->whenLoaded('detalleInsumos')),
        ];
    }
}
