<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Penitip;
use Illuminate\Support\Facades\Auth;

class LaporanTransaksiPenitip extends Page
{
    protected static string $view = 'filament.pages.laporan-transaksi-penitip';
    protected static ?string $navigationLabel = 'Laporan Transaksi Penitip';
    protected static ?string $navigationIcon  = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Laporan';

    public $penitipOptions;

    public function mount()
    {
        $this->penitipOptions = Penitip::pluck('NAMA_PENITIP', 'ID_PENITIP')->toArray();
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        return ($user instanceof \App\Models\Pegawai);
    }
}
