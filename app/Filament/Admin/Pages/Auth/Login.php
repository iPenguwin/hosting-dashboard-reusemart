<?php

namespace App\Filament\Admin\Pages\Auth;

use App\Models\Pegawai;
use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Form;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected static string $view = 'filament-panels::pages.auth.login';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label('Email Admin / Owner')
                    ->required()
                    ->email(),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->required(),
                Checkbox::make('remember')->label('Ingat saya'),
            ])
            ->statePath('data');
    }

    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        $pegawai = Pegawai::where('EMAIL_PEGAWAI', $data['email'])->first();

        if (!$pegawai || !Hash::check($data['password'], $pegawai->PASSWORD_PEGAWAI)) {
            throw ValidationException::withMessages([
                'data.email' => 'Kredensial salah.',
            ]);
        }

        $jabatan = strtolower(optional($pegawai->jabatans)->NAMA_JABATAN);

        if (!in_array($jabatan, ['admin', 'owner',])) {
            throw ValidationException::withMessages([
                'data.email' => 'Anda bukan Admin. Akses ditolak.',
            ]);
        }

        Auth::guard('pegawai')->login($pegawai, $data['remember'] ?? false);

        // \Log::info('User logged in with guard: ' . Auth::guard('pegawai')->user()->ID_PEGAWAI);

        session()->regenerate();

        return app(LoginResponse::class);
    }
}
