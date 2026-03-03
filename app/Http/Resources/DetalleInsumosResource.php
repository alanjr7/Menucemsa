<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetalleInsumosResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ID_FARMACIA' => $this->ID_FARMACIA,
            'CODIGO_INSUMOS' => $this->CODIGO_INSUMOS,
            'LABORATORIO' => $this->LABORATORIO,
            'FECHA_VENCIMIENTO' => $this->FECHA_VENCIMIENTO,
            'DESCRIPCION' => $this->DESCRIPCION,
            'farmacia' => new FarmaciaResource($this->whenLoaded('farmacia')),
            'insumo' => new InsumosResource($this->whenLoaded('insumo')),
        ];
    }
}
