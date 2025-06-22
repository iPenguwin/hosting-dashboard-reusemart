<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\HasDatabaseNotifications;
use Illuminate\Contracts\Auth\CanResetPassword;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class Penitip extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $guarded = [];

    protected $table = 'penitips';
    protected $primaryKey = 'ID_PENITIP';

    protected $fillable = [
        'NAMA_PENITIP',
        'PROFILE_PENITIP',
        'NO_KTP',
        'ALAMAT_PENITIP',
        'TGL_LAHIR_PENITIP',
        'NO_TELP_PENITIP',
        'EMAIL_PENITIP',
        'PASSWORD_PENITIP',
        // 'SALDO_PENITIP',
        'POINT_LOYALITAS_PENITIP',
        'POINT_LOYALITAS_PENITIP',
        'SALDO_PENITIP',
        'RATING_PENITIP',
        'remember_token',
    ];

    protected $hidden = [
        'PASSWORD_PENITIP',
        'POINT_LOYALITAS_PENITIP',
    ];

    protected $casts = [
        'PASSWORD_PENITIP' => 'hashed',
    ];

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'ID_PENITIP', 'ID_PENITIP');
    }

    public function getAuthPassword()
    {
        return $this->PASSWORD_PENITIP;
    }

    public function getEmailForPasswordReset()
    {
        return $this->EMAIL_PENITIP;
    }

    public function getNameAttribute(): string
    {
        return $this->NAMA_PENITIP ?? 'Penitip';
    }
        
    public function recalculateRating(): void
    {
        $avg = $this->barangs()
            ->whereNotNull('RATING')
            ->avg('RATING');

        $this->RATING_PENITIP = $avg !== null
            ? round($avg, 1)
            : 0;

        // save tanpa memicu event looping
        $this->saveQuietly();
    }

    public function badges()
    {
        return $this->hasMany(Badge::class, 'ID_PENITIP', 'ID_PENITIP');
    }

    public function komisis()
    {
        return $this->hasMany(Komisi::class, 'ID_PENITIP', 'ID_PENITIP');
    }

}
