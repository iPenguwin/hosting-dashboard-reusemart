<?php

namespace App\Policies;

use App\Models\Penitip;
use App\Models\Pegawai;
use Illuminate\Auth\Access\Response;

class PenitipPolicy
{
    /**
     * Jabatan yang diperbolehkan mengakses resource Penitip.
     */
    private array $allowedRoles = ['cs', 'admin', 'owner'];

    public function viewAny(Pegawai $pegawai): bool
    {
        return in_array(strtolower($pegawai->jabatan), $this->allowedRoles);
    }

    public function view(Pegawai $pegawai, Penitip $penitip): bool
    {
        return in_array(strtolower($pegawai->jabatan), $this->allowedRoles);
    }

    public function create(Pegawai $pegawai): bool
    {
        return in_array(strtolower($pegawai->jabatan), $this->allowedRoles);
    }

    public function update(Pegawai $pegawai, Penitip $penitip): bool
    {
        return in_array(strtolower($pegawai->jabatan), $this->allowedRoles);
    }

    public function delete(Pegawai $pegawai, Penitip $penitip): bool
    {
        return in_array(strtolower($pegawai->jabatan), $this->allowedRoles);
    }

    public function restore(Pegawai $pegawai, Penitip $penitip): bool
    {
        return in_array(strtolower($pegawai->jabatan), $this->allowedRoles);
    }

    public function forceDelete(Pegawai $pegawai, Penitip $penitip): bool
    {
        return in_array(strtolower($pegawai->jabatan), $this->allowedRoles);
    }
}
