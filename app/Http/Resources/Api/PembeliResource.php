<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PembeliResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ID_PEMBELI' => $this->ID_PEMBELI,
            'NAMA_PEMBELI' => $this->NAMA_PEMBELI,
            'TGL_LAHIR_PEMBELI' => $this->TGL_LAHIR_PEMBELI,
            'NO_TELP_PEMBELI' => $this->NO_TELP_PEMBELI,
            'EMAIL_PEMBELI' => $this->EMAIL_PEMBELI,
            'POINT_LOYALITAS_PEMBELI' => $this->POINT_LOYALITAS_PEMBELI,
            'PASSWORD_PEMBELI' => $this->PASSWORD_PEMBELI,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
