<?php

namespace App\Policies;

use App\Models\DetailTransaksiPenitipBarang;
use App\Models\Pegawai;
use Illuminate\Contracts\Auth\Authenticatable;

class DetailTransaksiPenitipBarangPolicy
{
    public function viewAny($user): bool
    {
        return false;
    }

    public function view(Authenticatable $user, DetailTransaksiPenitipBarang $model): bool
    {
        return $user instanceof Pegawai;
    }

    public function create(Authenticatable $user): bool
    {
        return $user instanceof Pegawai;
    }

    public function update(Authenticatable $user, DetailTransaksiPenitipBarang $model): bool
    {
        return $user instanceof Pegawai;
    }

    public function delete(Authenticatable $user, DetailTransaksiPenitipBarang $model): bool
    {
        return $user instanceof Pegawai;
    }

    public function restore(Authenticatable $user, DetailTransaksiPenitipBarang $model): bool
    {
        return $user instanceof Pegawai;
    }

    public function forceDelete(Authenticatable $user, DetailTransaksiPenitipBarang $model): bool
    {
        return $user instanceof Pegawai;
    }
}
