<?php

namespace App\Filament\Pegawai\Pages\Auth;

use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset; // Correct base class
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use App\Models\Pegawai;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label(__('Email'))
                    ->email()
                    ->required()
                    ->autocomplete('email')
                    ->autofocus(),
            ]);
    }

    public function mount(): void
    {
        if (auth(Filament::getAuthGuard())->check()) {
            redirect()->intended(Filament::getHomeUrl());
        }
    }

    public function request(): void
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('Email Tidak ditemukan!'))
                ->danger()
                ->send();
            return;
        }

        $data = $this->form->getState();

        $user = \App\Models\Pegawai::where('EMAIL_PEGAWAI', $data['email'])->first();

        if (!$user) {
            Notification::make()
                ->title(__('Email Tidak ditemukan!'))
                ->danger()
                ->send();
            return;
        }

        $token = Password::broker(Filament::getAuthPasswordBroker())->createToken($user);
        $resetUrl = Filament::getResetPasswordUrl($token, $user, ['EMAIL_PEGAWAI' => $user->EMAIL_PEGAWAI]);

        try {
            Mail::to($user->EMAIL_PEGAWAI)
                ->send(new \App\Mail\ResetPasswordMail($user->name, $resetUrl));

            Notification::make()
                ->title(__('Periksa Email untuk Reset Password'))
                ->success()
                ->send();

            $this->form->fill();
        } catch (\Exception $e) {
            Log::error('Error sending password reset email via Mail::raw: ' . $e->getMessage());
            Notification::make()
                ->title(__('filament-passwords::pages.password-reset.request.messages.failed'))
                ->danger()
                ->body('An error occurred while sending the email. Please try again later.')
                ->send();
        }
    }
}
