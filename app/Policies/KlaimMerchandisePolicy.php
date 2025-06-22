<?php

namespace App\Policies;

use App\Models\KlaimMerchandise;
use App\Models\Pegawai;
use Illuminate\Auth\Access\Response;

class KlaimMerchandisePolicy
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
    public function view(Pegawai $pegawai, KlaimMerchandise $klaimMerchandise): bool
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
    public function update(Pegawai $pegawai, KlaimMerchandise $klaimMerchandise): bool
    {
        return true;
    }

    /**
     * Determine whether the pegawai can delete the model.
     */
    public function delete(Pegawai $pegawai, KlaimMerchandise $klaimMerchandise): bool
    {
        return true;
    }

    /**
     * Determine whether the pegawai can restore the model.
     */
    public function restore(Pegawai $pegawai, KlaimMerchandise $klaimMerchandise): bool
    {
        return true;
    }

    /**
     * Determine whether the pegawai can permanently delete the model.
     */
    public function forceDelete(Pegawai $pegawai, KlaimMerchandise $klaimMerchandise): bool
    {
        return true;
    }
}
