<?php

namespace App\Policies;

use App\Models\TransaksiPembelianBarang;
use App\Models\Pembeli;
use App\Models\Pegawai;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TransaksiPembelianBarangPolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        if ($user instanceof Pegawai) {
            return in_array(strtolower($user->jabatan), ['admin', 'owner', 'cs', 'Kurir']);
        }
        if ($user instanceof Pembeli) {
            return true; // atau sesuaikan kebutuhan
        }
        return false;
    }

    public function view(Authenticatable $user, TransaksiPembelianBarang $transaksi): bool
    {
        // contoh logika akses view
        if ($user instanceof Pegawai) {
            return in_array(strtolower($user->jabatan), ['admin', 'owner', 'cs', 'Kurir']);
        }
        if ($user instanceof Pembeli) {
            return $transaksi->id_pembeli === $user->id;
        }
        return false;
    }

    public function create(Authenticatable $user): bool
    {
        if ($user instanceof Pegawai) {
            return in_array(strtolower($user->jabatan), ['admin', 'owner', 'cs', 'Kurir']);
        }
        // biasanya pembeli tidak bisa create transaksi manual
        return false;
    }

    public function update(Authenticatable $user, TransaksiPembelianBarang $transaksi): bool
    {
        if ($user instanceof Pegawai) {
            return in_array(strtolower($user->jabatan), ['admin', 'owner', 'cs', 'Kurir']);
        }
        if ($user instanceof Pembeli) {
            // contoh pembeli cuma boleh update transaksi miliknya sendiri
            return $transaksi->id_pembeli === $user->id;
        }
        return false;
    }

    public function delete(Authenticatable $user, TransaksiPembelianBarang $transaksi): bool
    {
        // sama dengan update, bisa disesuaikan
        if ($user instanceof Pegawai) {
            return in_array(strtolower($user->jabatan), ['admin', 'owner', 'cs', 'Kurir']);
        }
        if ($user instanceof Pembeli) {
            return $transaksi->id_pembeli === $user->id;
        }
        return false;
    }

    public function restore(Authenticatable $user, TransaksiPembelianBarang $transaksi): bool
    {
        return $this->update($user, $transaksi);
    }

    public function forceDelete(Authenticatable $user, TransaksiPembelianBarang $transaksi): bool
    {
        return $this->update($user, $transaksi);
    }
}
