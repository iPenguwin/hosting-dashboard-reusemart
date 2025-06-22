<?php

namespace App\Policies;

use App\Models\Kecamatan;
use App\Models\Pegawai;
use Illuminate\Auth\Access\Response;

class KecamatanPolicy
{
    /**
     * Determine whether the pegawai can view any models.
     */
    public function viewAny(Pegawai $pegawai): bool
    {
        return true;
    }

    /**
     * Determine whether the pegawai can view the model.
     */
    public function view(Pegawai $pegawai, Kecamatan $kecamatan): bool
    {
        return true;
    }

    /**
     * Determine whether the pegawai can create models.
     */
    public function create(Pegawai $pegawai): bool
    {
        return true;
    }

    /**
     * Determine whether the pegawai can update the model.
     */
    public function update(Pegawai $pegawai, Kecamatan $kecamatan): bool
    {
        return true;
    }

    /**
     * Determine whether the pegawai can delete the model.
     */
    public function delete(Pegawai $pegawai, Kecamatan $kecamatan): bool
    {
        return true;
    }

    /**
     * Determine whether the pegawai can restore the model.
     */
    public function restore(Pegawai $pegawai, Kecamatan $kecamatan): bool
    {
        return true;
    }

    /**
     * Determine whether the pegawai can permanently delete the model.
     */
    public function forceDelete(Pegawai $pegawai, Kecamatan $kecamatan): bool
    {
        return true;
    }
}
