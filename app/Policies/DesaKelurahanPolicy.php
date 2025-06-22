<?php

namespace App\Policies;

use App\Models\DesaKelurahan;
use App\Models\Pegawai;

class DesaKelurahanPolicy
{
    public function viewAny(Pegawai $pegawai): bool
    {
        return $pegawai->jabatans?->NAMA_JABATAN === 'Admin';
    }

    public function view(Pegawai $pegawai, DesaKelurahan $desaKelurahan): bool
    {
        return $pegawai->jabatans?->NAMA_JABATAN === 'Admin';
    }

    public function create(Pegawai $pegawai): bool
    {
        return $pegawai->jabatans?->NAMA_JABATAN === 'Admin';
    }

    public function update(Pegawai $pegawai, DesaKelurahan $desaKelurahan): bool
    {
        return $pegawai->jabatans?->NAMA_JABATAN === 'Admin';
    }

    public function delete(Pegawai $pegawai, DesaKelurahan $desaKelurahan): bool
    {
        return $pegawai->jabatans?->NAMA_JABATAN === 'Admin';
    }

    public function restore(Pegawai $pegawai, DesaKelurahan $desaKelurahan): bool
    {
        return $pegawai->jabatans?->NAMA_JABATAN === 'Admin';
    }

    public function forceDelete(Pegawai $pegawai, DesaKelurahan $desaKelurahan): bool
    {
        return $pegawai->jabatans?->NAMA_JABATAN === 'Admin';
    }
}
