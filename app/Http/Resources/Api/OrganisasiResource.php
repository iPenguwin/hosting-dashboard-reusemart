<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganisasiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ID_ORGANISASI' => $this->ID_ORGANISASI,
            'NAMA_ORGANISASI' => $this->NAMA_ORGANISASI,
            'ALAMAT_ORGANISASI' => $this->ALAMAT_ORGANISASI,
            'NO_TELP_ORGANISASI' => $this->NO_TELP_ORGANISASI,
            'EMAIL_ORGANISASI' => $this->EMAIL_ORGANISASI,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
