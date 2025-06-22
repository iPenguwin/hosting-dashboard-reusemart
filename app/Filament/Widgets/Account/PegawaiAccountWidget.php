<?php

namespace App\Filament\Widgets\Account;

use Filament\Widgets\AccountWidget as BaseAccountWidget;
use Illuminate\Support\Facades\Auth;

class PegawaiAccountWidget extends BaseAccountWidget
{
    protected static string $view = 'filament.widgets.pegawai-account-widget';

    public function getJabatan(): string
    {
        return Auth::user()->jabatan ?? '-';
    }

    public function getInitials(): string
    {
        $name = Auth::user()->NAMA_PEGAWAI;
        $words = explode(' ', $name);
        $initials = '';

        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }

        return substr($initials, 0, 2);
    }
}
