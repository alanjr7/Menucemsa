<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ID' => $this->ID,
            'ID_FARMACIA' => $this->ID_FARMACIA,
            'TIPO_ITEM' => $this->TIPO_ITEM,
            'STOCK_MINIMO' => $this->STOCK_MINIMO,
            'STOCK_DISPONIBLE' => $this->STOCK_DISPONIBLE,
            'REPOSICION' => $this->REPOSICION,
            'FECHA_INGRESO' => $this->FECHA_INGRESO,
            'farmacia' => new FarmaciaResource($this->whenLoaded('farmacia')),
        ];
    }
}
