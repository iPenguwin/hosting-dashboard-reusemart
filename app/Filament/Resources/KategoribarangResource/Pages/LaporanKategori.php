<?php

namespace App\Filament\Resources\KategoribarangResource\Pages;

use App\Filament\Resources\KategoribarangResource;
use App\Models\Kategoribarang;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class LaporanKategori extends Page
{
    protected static string $resource = KategoribarangResource::class;
    protected static string $view = 'filament.resources.kategoribarang-resource.pages.laporan-kategori';
    protected static ?string $navigationGroup = 'Laporan';

    public $tahun;
    public $categories;
    public $totalTerjual = 0;
    public $totalGagal = 0;
    public $totalHunter = 0;

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
        $this->tahun = date('Y');
        $this->loadData();
    }

    public function loadData()
    {
        $this->categories = Kategoribarang::with([
            'barangs' => function ($query) {
                $query->whereYear('TGL_MASUK', $this->tahun)
                    ->whereHas('pegawai', function ($q) {
                        $q->whereHas('jabatans', function ($subQuery) {
                            $subQuery->where('NAMA_JABATAN', 'Hunter');
                        });
                    })
                    ->with('pegawai');
            }
        ])
            ->withCount([
                'barangs as terjual' => function ($query) {
                    $query->where('STATUS_BARANG', 'Terjual')
                        ->whereNotNull('TGL_KELUAR')
                        ->whereYear('TGL_KELUAR', $this->tahun);
                },
                'barangs as gagal_terjual' => function ($query) {
                    $query->where(function ($q) {
                        $q->where('STATUS_BARANG', 'Didonasikan')
                            ->orWhere('STATUS_BARANG', 'Tidak Tersedia');
                    })
                        ->where(function ($q) {
                            $q->whereNotNull('TGL_AMBIL')
                                ->whereYear('TGL_AMBIL', $this->tahun)
                                ->orWhereNotNull('TGL_KELUAR')
                                ->whereYear('TGL_KELUAR', $this->tahun);
                        });
                },
                'barangs as hunter' => function ($query) {
                    $query->whereHas('pegawai', function ($q) {
                        $q->whereHas('jabatans', function ($subQuery) {
                            $subQuery->where('NAMA_JABATAN', 'Hunter');
                        });
                    })
                        ->whereYear('TGL_MASUK', $this->tahun);
                }
            ])
            ->orderBy('NAMA_KATEGORI')
            ->get()
            ->map(function ($category) {
                $hunterNames = $category->barangs
                    ->pluck('pegawai.NAMA_PEGAWAI')
                    ->unique()
                    ->filter()
                    ->values()
                    ->toArray();

                $category->hunter_names = $hunterNames;
                return $category;
            });

        $this->totalTerjual = $this->categories->sum('terjual');
        $this->totalGagal = $this->categories->sum('gagal_terjual');
        $this->totalHunter = $this->categories->sum('hunter');
    }

    public function updatedTahun()
    {
        $this->loadData();
    }

    protected function getViewData(): array
    {
        return [
            'year' => $this->tahun,
            'printDate' => now()->format('d F Y'),
            'categories' => $this->categories,
            'totalTerjual' => $this->totalTerjual,
            'totalGagal' => $this->totalGagal,
            'totalHunter' => $this->totalHunter,
        ];
    }

    public function generatePDF()
    {
        $this->loadData();

        $pdf = Pdf::loadView('pdf.laporan-kategori', $this->getViewData());

        return response()->streamDownload(
            fn() => print($pdf->output()),
            "Laporan_Penjualan_Kategori_{$this->tahun}.pdf"
        );
    }
}
