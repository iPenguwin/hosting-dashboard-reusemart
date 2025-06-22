<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaporanStokGudang extends Page
{
    protected static string $view = 'filament.pages.laporan-stok-gudang';
    protected static ?string $navigationLabel = 'Laporan Stok Gudang';
    protected static ?string $navigationIcon  = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Laporan';

    public \Illuminate\Support\Collection $stok;
    public string $tanggalCetak;

    public function mount(): void
    {
        // Set tanggal cetak (hari ini)
        $this->tanggalCetak = Carbon::now()
            ->locale('id')
            ->translatedFormat('j F Y');

        // Ambil data stok
        $this->stok = DB::table('barangs as b')
            ->leftJoin('penitips as p', 'b.ID_PENITIP', '=', 'p.ID_PENITIP')
            ->leftJoin('pegawais as h', 'b.ID_PEGAWAI', '=', 'h.ID_PEGAWAI')
            ->leftJoin('jabatans as j', 'h.ID_JABATAN', '=', 'j.ID_JABATAN')
            ->select([
                'b.KODE_BARANG',
                'b.NAMA_BARANG',
                'b.ID_PENITIP',
                'p.NAMA_PENITIP',
                'b.TGL_MASUK',
                DB::raw("(LOWER(b.STATUS_BARANG) = 'diperpanjang') AS perpanjangan"),
                DB::raw("CASE WHEN LOWER(j.NAMA_JABATAN) = 'hunter' THEN h.ID_PEGAWAI ELSE NULL END AS ID_HUNTER"),
                DB::raw("CASE WHEN LOWER(j.NAMA_JABATAN) = 'hunter' THEN h.NAMA_PEGAWAI ELSE NULL END AS nama_hunter"),
                'b.HARGA_BARANG as harga',
            ])
            ->whereIn('b.STATUS_BARANG', ['tersedia', 'diperpanjang'])
            ->orderBy('b.KODE_BARANG')
            ->get();
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        return
            ($user instanceof \App\Models\Pegawai)
            && strtolower($user->jabatan ?? '') === 'owner';
    }
}