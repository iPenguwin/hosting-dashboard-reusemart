<?php

namespace App\Policies;

use App\Models\Kategoribarang;
use App\Models\Pegawai;
use Illuminate\Auth\Access\Response;

class KategoribarangPolicy
{
    /**
     * Determine whether the pegawai can view any models.
     */
    public function viewAny($user): bool
    {
        if ($user instanceof Pegawai) {
            return in_array($user->JABATAN, ['Admin', 'Pegawai Gudang', 'Owner']);
        }

        return false;
    }

    /**
     * Determine whether the pegawai can view the model.
     */
    public function view($user): bool
    {
        if ($user instanceof Pegawai) {
            return in_array($user->JABATAN, ['Admin', 'Pegawai Gudang', 'Owner']);
        }

        return false;
    }

    /**
     * Determine whether the pegawai can create models.
     */
    public function create($user): bool
    {
        if ($user instanceof Pegawai) {
            return in_array($user->JABATAN, ['Admin', 'Pegawai Gudang', 'Owner']);
        }

        return false;
    }

    /**
     * Determine whether the pegawai can update the model.
     */
    public function update($user): bool
    {
        if ($user instanceof Pegawai) {
            return in_array($user->JABATAN, ['Admin', 'Pegawai Gudang', 'Owner']);
        }

        return false;
    }

    /**
     * Determine whether the pegawai can delete the model.
     */
    public function delete($user): bool
    {
        if ($user instanceof Pegawai) {
            return in_array($user->JABATAN, ['Admin', 'Pegawai Gudang', 'Owner']);
        }

        return false;
    }

    /**
     * Determine whether the pegawai can restore the model.
     */
    public function restore($user): bool
    {
        return true;
    }

    /**
     * Determine whether the pegawai can permanently delete the model.
     */
    public function forceDelete($user): bool
    {
        return true;
    }
}
