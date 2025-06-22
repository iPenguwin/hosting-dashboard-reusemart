<?php

namespace App\Policies;

use App\Models\Badge;
use App\Models\Pegawai;
use Illuminate\Auth\Access\Response;

class BadgePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Pegawai $pegawai): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Pegawai $pegawai, Badge $badge): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Pegawai $pegawai): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Pegawai $pegawai, Badge $badge): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Pegawai $pegawai, Badge $badge): bool
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Pegawai $pegawai, Badge $badge): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Pegawai $pegawai, Badge $badge): bool
    {
        return true;
    }
}
