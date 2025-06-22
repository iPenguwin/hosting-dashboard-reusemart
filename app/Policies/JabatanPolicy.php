<?php

namespace App\Policies;

use App\Models\Jabatan;
use App\Models\Pegawai;

class JabatanPolicy
{
    /**
     * Determine whether the Pegawai can view any positions.
     */
    public function viewAny(Pegawai $pegawai): bool
    {
        return true; // Full access to view all positions
    }

    /**
     * Determine whether the Pegawai can view a specific position.
     */
    public function view(Pegawai $pegawai, Jabatan $jabatan): bool
    {
        return true; // Full access to view any position
    }

    /**
     * Determine whether the Pegawai can create new positions.
     */
    public function create(Pegawai $pegawai): bool
    {
        return true; // Full access to create positions
    }

    /**
     * Determine whether the Pegawai can update a position.
     */
    public function update(Pegawai $pegawai, Jabatan $jabatan): bool
    {
        return true; // Full access to update any position
    }

    /**
     * Determine whether the Pegawai can delete a position.
     */
    public function delete(Pegawai $pegawai, Jabatan $jabatan): bool
    {
        return true; // Full access to delete any position
    }

    /**
     * Determine whether the Pegawai can restore a deleted position.
     */
    public function restore(Pegawai $pegawai, Jabatan $jabatan): bool
    {
        return true; // Full access to restore positions
    }

    /**
     * Determine whether the Pegawai can permanently delete a position.
     */
    public function forceDelete(Pegawai $pegawai, Jabatan $jabatan): bool
    {
        return true; // Full access to permanently delete
    }
}
