<?php

namespace App\Policies;

use App\Models\TransaksiDonasi;
use App\Models\Pegawai;
use App\Models\Organisasi;
use Illuminate\Auth\Access\Response;

class TransaksiDonasiPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny($user): bool
    {
        if ($user instanceof Organisasi) {
            return true; // Organisasi can view all transactions
        }

        if ($user instanceof Pegawai) {
            return $user->JABATAN === 'Owner'; // Only Owner Pegawai can view all
        }

        return false; // Deny by default
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, TransaksiDonasi $transaksiDonasi): bool
    {
        if ($user instanceof Organisasi) {
            // Organisasi can view if they own the transaction
            return $transaksiDonasi->ID_ORGANISASI === $user->ID_ORGANISASI;
        }

        if ($user instanceof Pegawai) {
            return $user->JABATAN === 'Owner'; // Only Owner Pegawai can view
        }

        return false; // Deny by default
    }

    /**
     * Determine whether the user can create models.
     */
    public function create($user): bool
    {
        if ($user instanceof Organisasi) {
            return true; // Organisasi can create transactions
        }

        if ($user instanceof Pegawai) {
            return $user->JABATAN === 'Owner'; // Only Owner Pegawai can create
        }

        return false; // Deny by default
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update($user, TransaksiDonasi $transaksiDonasi): bool
    {
        if ($user instanceof Organisasi) {
            // Organisasi can update if they own the transaction
            return $transaksiDonasi->ID_ORGANISASI === $user->ID_ORGANISASI;
        }

        if ($user instanceof Pegawai) {
            return $user->JABATAN === 'Owner'; // Only Owner Pegawai can update
        }

        return false; // Deny by default
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, TransaksiDonasi $transaksiDonasi): bool
    {
        if ($user instanceof Organisasi) {
            // Organisasi can delete if they own the transaction
            return $transaksiDonasi->ID_ORGANISASI === $user->ID_ORGANISASI;
        }

        if ($user instanceof Pegawai) {
            return $user->JABATAN === 'Owner'; // Only Owner Pegawai can delete
        }

        return false; // Deny by default
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, TransaksiDonasi $transaksiDonasi): bool
    {
        if ($user instanceof Organisasi) {
            // Organisasi can restore if they own the transaction
            return $transaksiDonasi->ID_ORGANISASI === $user->ID_ORGANISASI;
        }

        if ($user instanceof Pegawai) {
            return $user->JABATAN === 'Owner'; // Only Owner Pegawai can restore
        }

        return false; // Deny by default
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, TransaksiDonasi $transaksiDonasi): bool
    {
        if ($user instanceof Organisasi) {
            // Organisasi can force delete if they own the transaction
            return $transaksiDonasi->ID_ORGANISASI === $user->ID_ORGANISASI;
        }

        if ($user instanceof Pegawai) {
            return $user->JABATAN === 'Owner'; // Only Owner Pegawai can force delete
        }

        return false; // Deny by default
    }
}
