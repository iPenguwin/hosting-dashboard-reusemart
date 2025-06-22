<?php

namespace App\Http\Resources\Api;

use App\Filament\Resources\JabatanResource;
use App\Filament\Resources\KomisiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PegawaiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ID_PEGAWAI' => $this->ID_PEGAWAI,
            'ID_JABATAN' => $this->ID_JABATAN,
            'NAMA_PEGAWAI' => $this->NAMA_PEGAWAI,
            'NO_TELP_PEGAWAI' => $this->NO_TELP_PEGAWAI,
            'EMAIL_PEGAWAI' => $this->EMAIL_PEGAWAI,
            'PASSWORD_PEGAWAI' => $this->PASSWORD_PEGAWAI,
            'KOMISI_PEGAWAI' => $this->KOMISI_PEGAWAI,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'jabatan' => JabatanResource::make($this->jabatans),
            'komisi' => KomisiResource::collection($this->komisis),
            'links' => [
                'self' => route('pegawai.show', $this->ID_PEGAWAI),
                'jabatan' => route('jabatan.show', $this->ID_JABATAN),
                'komisi' => route('komisi.index', ['ID_PEGAWAI' => $this->ID_PEGAWAI]),
            ]
        ];
    }
}
