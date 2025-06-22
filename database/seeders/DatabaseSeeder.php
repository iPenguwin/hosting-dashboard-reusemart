<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pegawai;
use App\Models\Jabatan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\Kategoribarang;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminJabatan = Jabatan::firstOrCreate(['NAMA_JABATAN' => 'Admin']);
        $ownerJabatan = Jabatan::firstOrCreate(['NAMA_JABATAN' => 'Owner']);
        $gudangJabatan = Jabatan::firstOrCreate(['NAMA_JABATAN' => 'Pegawai Gudang']);
        $csJabatan = Jabatan::firstOrCreate(['NAMA_JABATAN' => 'CS']);
        $hunterJabatan = Jabatan::firstOrCreate(['NAMA_JABATAN' => 'Hunter']);
        $kurirJabatan = Jabatan::firstOrCreate(['NAMA_JABATAN' => 'Kurir']);

        Pegawai::create([
            'ID_JABATAN' => $ownerJabatan->ID_JABATAN,
            'NAMA_PEGAWAI' => 'Owner ReuseMart',
            'NO_TELP_PEGAWAI' => '081234567890',
            'EMAIL_PEGAWAI' => 'owner@reusermart.test',
            'PASSWORD_PEGAWAI' => Hash::make('password'),
            'KOMISI_PEGAWAI' => 0,
            'TGL_LAHIR_PEGAWAI' => now()->subYears(rand(20, 50))->subDays(rand(0, 365))->format('Y-m-d'),
        ]);

        $kategori = [
            'Elektronik & Gadget',
            'Pakaian & Aksesori',
            'Perabotan Rumah Tangga',
            'Buku, Alat Tulis, & Peralatan Sekolah',
            'Hobi, Mainan, & Koleksi',
            'Perlengkapan Bayi & Anak',
            'Otomotif & Aksesori',
            'Perlengkapan Taman & Outdoor',
            'Peralatan Kantor & Industri',
            'Kosmetik & Perawatan Diri',
        ];

        foreach ($kategori as $nama) {
            Kategoribarang::firstOrCreate(['NAMA_KATEGORI' => $nama]);
        }
    }
}
