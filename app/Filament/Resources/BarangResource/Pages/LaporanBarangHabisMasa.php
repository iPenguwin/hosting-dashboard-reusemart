<?php

namespace App\Filament\Resources\BarangResource\Pages;

use App\Filament\Resources\BarangResource;
use App\Models\Barang;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class LaporanTitipanMasaHabis extends Page
{
    protected static string $resource = BarangResource::class;
    protected static string $view = 'filament.resources.barang-resource.pages.laporan-titipan-masa-habis';

    public $barangs;
    public $printDate;

    protected function getActions(): array
    {
        return [
            Action::make('cetak_laporan')
                ->label('Cetak Laporan')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->action('generatePDF')
        ];
    }

    public function mount()
    {
        $this->printDate = now()->format('d F Y');
        $this->loadData();
    }

    public function loadData()
    {
        $today = Carbon::now();

        $this->barangs = Barang::where(function ($query) use ($today) {
            // Barang dengan status Tersedia yang sudah melewati masa titipan + 2 hari
            $query->where('STATUS_BARANG', 'Tersedia')
                ->whereDate('TGL_KELUAR', '<=', $today->subDays(2));
        })
            ->orWhere(function ($query) use ($today) {
                // Barang dengan status Diperpanjang yang sudah melewati masa titipan + 2 hari
                $query->where('STATUS_BARANG', 'Diperpanjang')
                    ->whereDate('TGL_KELUAR', '<=', $today->subDays(2));
            })
            ->with(['penitip'])
            ->orderBy('TGL_KELUAR')
            ->get();
    }

    protected function getViewData(): array
    {
        return [
            'barangs' => $this->barangs,
            'printDate' => $this->printDate,
        ];
    }

    public function generatePDF()
    {
        $this->loadData();

        $pdf = Pdf::loadView('pdf.laporan-titipan-masa-habis', $this->getViewData());

        return response()->streamDownload(
            fn() => print($pdf->output()),
            "Laporan_Barang_Titipan_Masa_Habis_" . now()->format('Y-m-d') . ".pdf"
        );
    }
}
