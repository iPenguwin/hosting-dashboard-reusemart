<?php

namespace App\Policies;

use App\Models\Komisi;
use App\Models\Pegawai;
use Illuminate\Auth\Access\Response;

class KomisiPolicy
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
    public function view(Pegawai $pegawai, Komisi $komisi): bool
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
    public function update(Pegawai $pegawai, Komisi $komisi): bool
    {
        return true;
    }

    /**
     * Determine whether the pegawai can delete the model.
     */
    public function delete(Pegawai $pegawai, Komisi $komisi): bool
    {
        return true;
    }

    /**
     * Determine whether the pegawai can restore the model.
     */
    public function restore(Pegawai $pegawai, Komisi $komisi): bool
    {
        return true;
    }

    /**
     * Determine whether the pegawai can permanently delete the model.
     */
    public function forceDelete(Pegawai $pegawai, Komisi $komisi): bool
    {
        return true;
    }
}
