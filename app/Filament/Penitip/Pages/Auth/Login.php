<?php

namespace App\Filament\Penitip\Pages\Auth;

use App\Models\Penitip;
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label('Email Penitip')
                    ->required()
                    ->email(),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->required()
                    ->hint(
                        new HtmlString(
                            Blade::render(
                                '<div class="flex flex-col gap-2">' .
                                    (Filament::hasPasswordReset() ? '<x-filament::link :href="filament()->getRequestPasswordResetUrl()" tabindex="3"> {{ __(\'filament-panels::pages/auth/login.actions.request_password_reset.label\') }}</x-filament::link>' : '') .
                                    '<x-filament::link :href="route(\'magic-link-login\')" tabindex="4">Login with Magic Link</x-filament::link>' .
                                    '</div>'
                            )
                        )
                    ),
                Checkbox::make('remember')->label('Ingat saya'),
            ])
            ->statePath('data');
    }

    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        $penitip = Penitip::where('EMAIL_PENITIP', $data['email'])->first();

        if (!$penitip || !Hash::check($data['password'], $penitip->PASSWORD_PENITIP)) {
            throw ValidationException::withMessages([
                'data.email' => 'Kredensial salah.',
            ]);
        }

        Auth::guard('penitip')->login($penitip, $data['remember'] ?? false);

        session()->regenerate();

        return app(LoginResponse::class);
    }
}
