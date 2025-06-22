<?php

namespace App\Filament\Organisasi\Pages\Auth;

use App\Models\Organisasi;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected static string $view = 'filament-panels::pages.auth.login';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label('Email Organisasi')
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

        $organisasi = Organisasi::where('EMAIL_ORGANISASI', $data['email'])->first();

        if (! $organisasi || ! Hash::check($data['password'], $organisasi->PASSWORD_ORGANISASI)) {
            throw ValidationException::withMessages([
                'data.email' => 'Email atau password salah.',
            ]);
        }

        Auth::guard('organisasi')->login($organisasi, $data['remember'] ?? false);

        session()->regenerate();

        return app(LoginResponse::class);
    }
}
