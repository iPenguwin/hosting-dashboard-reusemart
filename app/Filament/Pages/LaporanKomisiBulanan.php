<?php
namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanKomisiBulanan extends Page
{
    protected static string $view = 'filament.pages.laporan-komisi-bulanan';
    protected static ?string $navigationLabel = 'Laporan Komisi Bulanan';
    protected static ?string $navigationIcon  = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Laporan';

    // properti untuk form & data
    public int   $bulan;
    public int   $tahun;
    public \Illuminate\Support\Collection $dataRows;
    public float $grandHunter;
    public float $grandReUseMart;
    public float $grandBonusPenitip;
    public string $namaBulan;
    public string $tanggalCetak;

    public function mount(): void
    {
        // 1) Baca parameter
        $this->bulan = (int) request()->query('bulan', Carbon::now()->month);
        $this->tahun = (int) request()->query('tahun', Carbon::now()->year);

        // 2) Nama bulan
        $namaBulanList = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
            5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
            9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
        ];
        $this->namaBulan = $namaBulanList[$this->bulan] ?? 'Unknown';

        // 3) Ambil transaksi Lunas
        $transaksis = DB::table('transaksi_pembelian_barangs as t')
            ->select([
                'b.KODE_BARANG','b.NAMA_BARANG',
                't.TOT_HARGA_PEMBELIAN as harga_jual',
                't.TGL_PESAN_PEMBELIAN as tanggal_masuk',
                't.TGL_PESAN_PEMBELIAN as tanggal_laku_simulasi',
                'b.ID_PEGAWAI as id_hunter',
                'h.NAMA_PEGAWAI as nama_hunter',
                'j.NAMA_JABATAN as nama_jabatan',
            ])
            ->join('barangs as b', 'b.ID_BARANG', '=', 't.ID_BARANG')
            ->leftJoin('pegawais as h', 'h.ID_PEGAWAI', '=', 'b.ID_PEGAWAI')
            ->leftJoin('jabatans as j', 'j.ID_JABATAN', '=', 'h.ID_JABATAN')
            ->where('t.STATUS_PEMBAYARAN','Lunas')
            ->whereRaw('MONTH(t.TGL_PESAN_PEMBELIAN)=?',[$this->bulan])
            ->whereRaw('YEAR(t.TGL_PESAN_PEMBELIAN)=?',[$this->tahun])
            ->orderBy('b.KODE_BARANG')
            ->get();

        // 4) Hitung komisi
        $rows = collect();
        foreach ($transaksis as $t) {
            $hargaJual = (float) $t->harga_jual;
            $tglMasuk  = Carbon::parse($t->tanggal_masuk);
            $tglLaku   = Carbon::parse($t->tanggal_laku_simulasi);
            $hariTerjual = $tglMasuk->diffInDays($tglLaku);

            $adaHunter = $t->id_hunter !== null && strtolower($t->nama_jabatan)==='hunter';

            $komisiHunter = $komisiBase = $bonusPenitip = $komisiReUseMartBersih = 0.0;

            if ($hariTerjual <= 7) {
                $komisiBase         = 0.20 * $hargaJual;
                $bonusPenitip       = 0.10 * $komisiBase;
                if ($adaHunter) {
                    $komisiHunter       = 0.05 * $hargaJual;
                    $komisiReUseMartBersih = $komisiBase - $komisiHunter - $bonusPenitip;
                } else {
                    $komisiReUseMartBersih = $komisiBase - $bonusPenitip;
                }
            } elseif ($hariTerjual > 30) {
                $komisiReUseMartBersih = 0.275 * $hargaJual;
            }

            $rows->push([
                'kode_produk'      => $t->KODE_BARANG,
                'nama_produk'      => $t->NAMA_BARANG,
                'harga_jual'       => $hargaJual,
                'tanggal_masuk'    => $tglMasuk->format('j/n/Y'),
                'tanggal_laku'     => $tglLaku->format('j/n/Y'),
                'komisi_hunter'    => $komisiHunter,
                'komisi_reusemart' => $komisiReUseMartBersih,
                'bonus_penitip'    => $bonusPenitip,
            ]);
        }

        // 5) Hitung grand total
        $this->dataRows         = $rows;
        $this->grandHunter      = $rows->sum('komisi_hunter');
        $this->grandReUseMart   = $rows->sum('komisi_reusemart');
        $this->grandBonusPenitip= $rows->sum('bonus_penitip');
        $this->tanggalCetak     = Carbon::now()->locale('id')->translatedFormat('j F Y');
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user    = Auth::user();
        $jabatan = $user->jabatan ?? '';
        return
            ($user instanceof \App\Models\Pegawai)
            && strtolower($jabatan) === 'owner';
    }
}