<?php

namespace App\Policies;

use App\Models\Alamat;
use App\Models\Pembeli;

class AlamatPolicy
{
    /**
     * Determine whether the Pembeli can view any models.
     */
    public function viewAny(Pembeli $pembeli): bool
    {
        return true; // Pembeli can view their own addresses
    }

    /**
     * Determine whether the Pembeli can view the model.
     */
    public function view(Pembeli $pembeli, Alamat $alamat): bool
    {
        return $pembeli->id_pembeli === $alamat->id_pembeli;
    }

    /**
     * Determine whether the Pembeli can create models.
     */
    public function create(Pembeli $pembeli): bool
    {
        return true;
    }

    /**
     * Determine whether the Pembeli can update the model.
     */
    public function update(Pembeli $pembeli, Alamat $alamat): bool
    {
        return $pembeli->id_pembeli === $alamat->id_pembeli;
    }

    /**
     * Determine whether the Pembeli can delete the model.
     */
    public function delete(Pembeli $pembeli, Alamat $alamat): bool
    {
        return $pembeli->id_pembeli === $alamat->id_pembeli;
    }

    /**
     * Determine whether the Pembeli can restore the model.
     */
    public function restore(Pembeli $pembeli, Alamat $alamat): bool
    {
        return false;
    }

    /**
     * Determine whether the Pembeli can permanently delete the model.
     */
    public function forceDelete(Pembeli $pembeli, Alamat $alamat): bool
    {
        return false;
    }
}
