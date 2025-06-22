<?php

namespace App\Policies;

use App\Models\Request;
use App\Models\Pegawai;
use App\Models\Organisasi;

class RequestPolicy
{
    /**
     * Determine whether the user can view any requests.
     */
    public function viewAny($user): bool
    {
        if ($user instanceof Organisasi) {
            return true; // Organisasi can view their own requests
        }

        if ($user instanceof Pegawai) {
            $jabatan = strtolower($user->jabatan);
            return in_array($jabatan, ['admin', 'owner']);
        }

        return false;
    }

    /**
     * Determine whether the user can view the request.
     */
    public function view($user, Request $request): bool
    {
        if ($user instanceof Organisasi) {
            return $request->ID_ORGANISASI === $user->ID_ORGANISASI;
        }

        if ($user instanceof Pegawai) {
            $jabatan = strtolower($user->jabatan);
            return in_array($jabatan, ['admin', 'owner']);
        }

        return false;
    }

    /**
     * Determine whether the user can create requests.
     */
    public function create($user): bool
    {
        if ($user instanceof Organisasi) {
            return true;
        }

        if ($user instanceof Pegawai) {
            $jabatan = strtolower($user->jabatan);
            return in_array($jabatan, ['admin', 'owner']);
        }

        return false;
    }

    /**
     * Determine whether the user can update the request.
     */
    public function update($user, Request $request): bool
    {
        if ($user instanceof Organisasi) {
            return $request->ID_ORGANISASI === $user->ID_ORGANISASI;
        }

        if ($user instanceof Pegawai) {
            $jabatan = strtolower($user->jabatan);
            return in_array($jabatan, ['admin', 'owner']);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the request.
     */
    public function delete($user, Request $request): bool
    {
        if ($user instanceof Organisasi) {
            return $request->ID_ORGANISASI === $user->ID_ORGANISASI;
        }

        if ($user instanceof Pegawai) {
            $jabatan = strtolower($user->jabatan);
            return in_array($jabatan, ['admin', 'owner']);
        }

        return false;
    }

    /**
     * Determine whether the user can restore the request.
     */
    public function restore($user, Request $request): bool
    {
        if ($user instanceof Organisasi) {
            return $request->ID_ORGANISASI === $user->ID_ORGANISASI;
        }

        if ($user instanceof Pegawai) {
            $jabatan = strtolower($user->jabatan);
            return in_array($jabatan, ['admin', 'owner']);
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the request.
     */
    public function forceDelete($user, Request $request): bool
    {
        if ($user instanceof Organisasi) {
            return $request->ID_ORGANISASI === $user->ID_ORGANISASI;
        }

        if ($user instanceof Pegawai) {
            $jabatan = strtolower($user->jabatan);
            return in_array($jabatan, ['admin', 'owner']);
        }

        return false;
    }
}
