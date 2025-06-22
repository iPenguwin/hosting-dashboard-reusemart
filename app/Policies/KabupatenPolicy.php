<?php

namespace App\Policies;

use App\Models\Kabupaten;
use App\Models\Pegawai;
use Illuminate\Auth\Access\Response;

class KabupatenPolicy
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
    public function view(Pegawai $pegawai, Kabupaten $kabupaten): bool
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
    public function update(Pegawai $pegawai, Kabupaten $kabupaten): bool
    {
        return true;
    }

    /**
     * Determine whether the pegawai can delete the model.
     */
    public function delete(Pegawai $pegawai, Kabupaten $kabupaten): bool
    {
        return true;
    }

    /**
     * Determine whether the pegawai can restore the model.
     */
    public function restore(Pegawai $pegawai, Kabupaten $kabupaten): bool
    {
        return true;
    }

    /**
     * Determine whether the pegawai can permanently delete the model.
     */
    public function forceDelete(Pegawai $pegawai, Kabupaten $kabupaten): bool
    {
        return true;
    }
}
