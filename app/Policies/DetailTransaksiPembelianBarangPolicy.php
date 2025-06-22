<?php

namespace App\Policies;

use App\Models\DetailTransaksiPembelianBarang;
use App\Models\Pegawai;

use Illuminate\Contracts\Auth\Authenticatable;

class DetailTransaksiPembelianBarangPolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return $user instanceof Pegawai;
    }

    public function view(Authenticatable $user, DetailTransaksiPembelianBarang $model): bool
    {
        return $user instanceof Pegawai;
    }

    public function create(Authenticatable $user): bool
    {
        return $user instanceof Pegawai;
    }

    public function update(Authenticatable $user, DetailTransaksiPembelianBarang $model): bool
    {
        return $user instanceof Pegawai;
    }

    public function delete(Authenticatable $user, DetailTransaksiPembelianBarang $model): bool
    {
        return $user instanceof Pegawai;
    }

    public function restore(Authenticatable $user, DetailTransaksiPembelianBarang $model): bool
    {
        return $user instanceof Pegawai;
    }

    public function forceDelete(Authenticatable $user, DetailTransaksiPembelianBarang $model): bool
    {
        return $user instanceof Pegawai;
    }
}
