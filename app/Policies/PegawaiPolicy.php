<?php

namespace App\Policies;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PegawaiPolicy
{
    public function viewAny(Pegawai $user): bool
    {
        return true;
    }
    public function view(Pegawai $user, Pegawai $model): bool
    {
        return true;
    }

    public function create(Pegawai $user): bool
    {
        return in_array(strtolower($user->jabatan), ['admin', 'owner']);
    }

    public function update(Pegawai $user, Pegawai $pegawai): bool
    {
        return in_array(strtolower($user->jabatan), ['admin', 'owner']);
    }

    public function delete(Pegawai $user, Pegawai $pegawai): bool
    {
        return in_array(strtolower($user->jabatan), ['admin', 'owner']);
    }

    public function restore(Pegawai $user, Pegawai $pegawai): bool
    {
        return in_array(strtolower($user->jabatan), ['admin', 'owner']);
    }

    public function forceDelete(Pegawai $user, Pegawai $pegawai): bool
    {
        return in_array(strtolower($user->jabatan), ['admin', 'owner']);
    }
}
