<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CajaFarmaciaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'CODIGO' => $this->CODIGO,
            'DETALLE' => $this->DETALLE,
            'TOTAL' => (float) $this->TOTAL,
            'ID_CAJA' => $this->ID_CAJA,
            'caja' => $this->whenLoaded('caja'),
        ];
    }
}
