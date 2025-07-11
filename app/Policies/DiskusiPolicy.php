<?php

namespace App\Policies;

use App\Models\Diskusi;
use App\Models\Pegawai;

class DiskusiPolicy
{
    /**
     * Determine whether the Pegawai can view any discussions.
     */
    public function viewAny(Pegawai $pegawai): bool
    {
        return true; // Full access to view all discussions
    }

    /**
     * Determine whether the Pegawai can view a specific discussion.
     */
    public function view(Pegawai $pegawai, Diskusi $diskusi): bool
    {
        return true; // Full access to view any discussion
    }

    /**
     * Determine whether the Pegawai can create new discussions.
     */
    public function create(Pegawai $pegawai): bool
    {
        return true; // Full access to create discussions
    }

    /**
     * Determine whether the Pegawai can update a discussion.
     */
    public function update(Pegawai $pegawai, Diskusi $diskusi): bool
    {
        return true; // Full access to update any discussion
    }

    /**
     * Determine whether the Pegawai can delete a discussion.
     */
    public function delete(Pegawai $pegawai, Diskusi $diskusi): bool
    {
        return true; // Full access to delete any discussion
    }

    /**
     * Determine whether the Pegawai can restore a deleted discussion.
     */
    public function restore(Pegawai $pegawai, Diskusi $diskusi): bool
    {
        return true; // Full access to restore discussions
    }

    /**
     * Determine whether the Pegawai can permanently delete a discussion.
     */
    public function forceDelete(Pegawai $pegawai, Diskusi $diskusi): bool
    {
        return true; // Full access to permanently delete
    }
}
