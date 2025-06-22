<?php

namespace App\Filament\Pegawai\Pages\Auth;

use App\Models\Pegawai;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Validation\ValidationException;
use Filament\Facades\Filament;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;

class Login extends BaseLogin
{
    protected static string $view = 'filament-panels::pages.auth.login';

    // No need for this property as the hint will check Filament::hasPasswordReset()
    // protected bool $hasPasswordReset = true;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label('Email Pegawai')
                    ->required()
                    ->email(),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->required()
                    // Add the hint for the password reset link
                    ->hint(Filament::hasPasswordReset() ? new HtmlString(Blade::render('<x-filament::link :href="filament()->getRequestPasswordResetUrl()" tabindex="3"> {{ __(\'filament-panels::pages/auth/login.actions.request_password_reset.label\') }}</x-filament::link>')) : null),
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

        if (!in_array($jabatan, ['hunter', 'cs', 'pegawai gudang', 'owner', 'pegawai', 'kurir'])) {
            throw ValidationException::withMessages([
                'data.email' => 'Anda tidak diizinkan mengakses panel ini.',
            ]);
        }

        Auth::guard('pegawai')->login($pegawai, $data['remember'] ?? false);

        session()->regenerate();

        // Mengembalikan LoginResponse dari Filament
        return app(LoginResponse::class);
    }

    // You can remove this method if you're relying on Filament::hasPasswordReset()
    // public function hasPasswordResetLink(): bool
    // {
    //     return true;
    // }
}
