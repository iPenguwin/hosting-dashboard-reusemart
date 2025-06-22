<?php

namespace App\Policies;

use App\Models\PegawaiTransaksiPenitipan;
use App\Models\Pegawai;
use Illuminate\Auth\Access\Response;

class PegawaiTransaksiPenitipanPolicy
{
    /**
     * Determine whether the pegawai can view any models.
     */
    public function viewAny($user): bool
    {
        if ($user instanceof Pegawai) {
            return in_array($user->JABATAN, ['Admin',]);
        }

        return false;
    }

    /**
     * Determine whether the pegawai can view the model.
     */
    public function view(Pegawai $pegawai, PegawaiTransaksiPenitipan $pegawaiTransaksiPenitipan): bool
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
    public function update(Pegawai $pegawai, PegawaiTransaksiPenitipan $pegawaiTransaksiPenitipan): bool
    {
        return true;
    }

    /**
     * Determine whether the pegawai can delete the model.
     */
    public function delete(Pegawai $pegawai, PegawaiTransaksiPenitipan $pegawaiTransaksiPenitipan): bool
    {
        return true;
    }

    /**
     * Determine whether the pegawai can restore the model.
     */
    public function restore(Pegawai $pegawai, PegawaiTransaksiPenitipan $pegawaiTransaksiPenitipan): bool
    {
        return true;
    }

    /**
     * Determine whether the pegawai can permanently delete the model.
     */
    public function forceDelete(Pegawai $pegawai, PegawaiTransaksiPenitipan $pegawaiTransaksiPenitipan): bool
    {
        return true;
    }
}
