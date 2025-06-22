<?php

namespace App\Models;

use illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Pembeli extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'pembelis';
    protected $primaryKey = 'ID_PEMBELI';

    protected $fillable = [
        'NAMA_PEMBELI',
        'PROFILE_PEMBELI',
        'TGL_LAHIR_PEMBELI',
        'NO_TELP_PEMBELI',
        'EMAIL_PEMBELI',
        'POINT_LOYALITAS_PEMBELI',
        'PASSWORD_PEMBELI',
    ];

    protected $hidden = [
        'PASSWORD_PEMBELI',
    ];

    public function alamats()
    {
        return $this->hasMany(Alamat::class, 'ID_PEMBELI', 'ID_PEMBELI');
    }

    public function transaksiPembelian()
    {
        return $this->hasMany(TransaksiPembelianBarang::class, 'ID_PEMBELI', 'ID_PEMBELI');
    }
    
    public function getAuthPassword()
    {
        return $this->PASSWORD_PEMBELI;
    }

    public function getEmailForPasswordReset()
    {
        return $this->EMAIL_PEMBELI;
    }
}

