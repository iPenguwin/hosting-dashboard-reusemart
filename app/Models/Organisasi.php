<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Organisasi extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $guarded = [];
    protected $table = 'organisasis';
    protected $primaryKey = 'ID_ORGANISASI';

    protected $fillable = [
        'NAMA_ORGANISASI',
        'PROFILE_ORGANISASI',
        'ALAMAT_ORGANISASI',
        'NO_TELP_ORGANISASI',
        'EMAIL_ORGANISASI',
        'PASSWORD_ORGANISASI',
    ];

    protected $hidden = [
        'PASSWORD_ORGANISASI',
    ];

    public function getAuthPassword()
    {
        return $this->PASSWORD_ORGANISASI;
    }

    public function getNameAttribute(): string
    {
        return $this->NAMA_ORGANISASI ?? 'Organisasi';
    }
}
