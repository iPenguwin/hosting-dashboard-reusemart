<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use App\Models\User;
use App\Models\Jabatan;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Notifications\Notifiable;

use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;

class Pegawai extends Authenticatable implements FilamentUser, CanResetPassword
{
    use HasApiTokens, HasFactory, CanResetPasswordTrait, Notifiable;

    protected $table = 'pegawais';
    protected $primaryKey = 'ID_PEGAWAI';
    protected $guarded = [];

    protected $fillable = [
        'ID_JABATAN',
        'NAMA_PEGAWAI',
        'PROFILE_PEGAWAI',
        'NO_TELP_PEGAWAI',
        'EMAIL_PEGAWAI',
        'PASSWORD_PEGAWAI',
        'KOMISI_PEGAWAI',
        'TGL_LAHIR_PEGAWAI',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'PASSWORD_PEGAWAI', // Hide the password attribute
        'remember_token',   // If you have a remember_token column
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pegawai) {
            $jabatan = Jabatan::find($pegawai->ID_JABATAN);

            if ($jabatan && strtolower($jabatan->NAMA_JABATAN) === 'hunter') {
                $pegawai->KOMISI_PEGAWAI = 0;
            } else {
                $pegawai->KOMISI_PEGAWAI = 0;
            }
        });

        static::created(function ($pegawai) {
            $jabatan = Jabatan::find($pegawai->ID_JABATAN);

            if ($jabatan && in_array(strtolower($jabatan->NAMA_JABATAN), ['Admin', 'Owner'])) {
                $user = new User();
                $user->name = $pegawai->NAMA_PEGAWAI;
                $user->email = $pegawai->EMAIL_PEGAWAI;
                $user->password = Hash::make($pegawai->PASSWORD_PEGAWAI);
                $user->save();
            }
        });
    }

    public function jabatans()
    {
        return $this->belongsTo(Jabatan::class, 'ID_JABATAN', 'ID_JABATAN');
    }

    public function komisis()
    {
        return $this->hasMany(Komisi::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }

    public function getJabatanAttribute()
    {
        return $this->jabatans->NAMA_JABATAN ?? null;
    }

    protected $appends = ['name'];

    public function getNameAttribute(): string
    {
        return $this->NAMA_PEGAWAI;
    }

    public function getFilamentUserName(): string
    {
        return $this->EMAIL_PEGAWAI ?? 'pegawai';
    }

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->EMAIL_PEGAWAI;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->PASSWORD_PEGAWAI;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName(): string
    {
        return 'EMAIL_PEGAWAI'; // THIS IS THE CRUCIAL LINE FOR THIS ERROR!
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        $jabatan = strtolower($this->jabatan);
        $panelId = $panel->getId();

        if ($panelId === 'admin') {
            return in_array($jabatan, ['admin', 'owner']);
        }

        if ($panelId === 'pegawai') {
            return in_array($jabatan, ['admin', 'owner', 'pegawai', 'hunter', 'cs', 'pegawai gudang', 'kurir']);
        }

        return false;
    }
}
