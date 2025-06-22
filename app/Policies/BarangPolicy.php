<?php

namespace App\Policies;

use App\Models\Barang;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Penitip;

class BarangPolicy
{
    /**
     * Semua pegawai bisa melihat daftar barang, penitip tidak.
     */
    public function viewAny($user): bool
    {
        if ($user instanceof Pegawai) {
            return in_array($user->JABATAN, ['Admin', 'Pegawai Gudang', 'Owner']);
        }

        // Penitip can view any of their own items (handled by query scope)
        return $user instanceof Penitip;
    }

    public function view($user, Barang $barang): bool
    {
        if ($user instanceof Pegawai) {
            return in_array($user->JABATAN, ['Admin', 'Pegawai Gudang', 'Owner']);
        }

        if ($user instanceof Penitip) {
            return $barang->ID_PENITIP === $user->ID_PENITIP;
        }

        return false;
    }

    /**
     * Hanya pegawai tertentu yang boleh membuat barang (bisa kamu sesuaikan).
     */
    public function create($user): bool
    {
        return $user instanceof Pegawai && in_array($user->JABATAN, ['Admin', 'Pegawai Gudang', 'Owner']);
    }

    /**
     * Hanya admin atau pegawai yang bisa update.
     */
    public function update($user): bool
    {
        if ($user instanceof Pegawai && in_array($user->JABATAN, ['Admin', 'Pegawai Gudang', 'Owner'])) {
            return true;
        }

        // Penitip juga BISA update
        return $user instanceof Penitip;
    }

    /**
     * Hanya admin yang bisa hapus.
     */
    public function delete($user): bool
    {
        return $user instanceof Pegawai && in_array($user->JABATAN, ['Admin', 'Pegawai Gudang', 'Owner']);
    }

    public function restore($user, Barang $barang): bool
    {
        return false;
    }

    public function forceDelete($user, Barang $barang): bool
    {
        return false;
    }
}
