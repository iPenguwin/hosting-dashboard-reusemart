<?php

namespace Database\Seeders;

use App\Models\Kecamatan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class KecamatanSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key constraints (PostgreSQL way)
        Schema::disableForeignKeyConstraints();

        // Truncate the table
        Kecamatan::truncate();

        // Re-enable foreign key constraints
        Schema::enableForeignKeyConstraints();

        // Disable foreign key constraints (MySQL way)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate the table
        Kecamatan::truncate();

        // Re-enable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $kecamatans = [
            ['id_kecamatan' => 2948, 'id_kabupaten_kota' => 210, 'nama_kecamatan' => 'Moyudan'],
            ['id_kecamatan' => 2949, 'id_kabupaten_kota' => 210, 'nama_kecamatan' => 'Minggir'],
            ['id_kecamatan' => 2950, 'id_kabupaten_kota' => 210, 'nama_kecamatan' => 'Seyegan'],
            ['id_kecamatan' => 2951, 'id_kabupaten_kota' => 210, 'nama_kecamatan' => 'Godean'],
            ['id_kecamatan' => 2952, 'id_kabupaten_kota' => 210, 'nama_kecamatan' => 'Gamping'],
            ['id_kecamatan' => 2953, 'id_kabupaten_kota' => 210, 'nama_kecamatan' => 'Mlati'],
            ['id_kecamatan' => 2954, 'id_kabupaten_kota' => 210, 'nama_kecamatan' => 'Depok'],
            ['id_kecamatan' => 2955, 'id_kabupaten_kota' => 210, 'nama_kecamatan' => 'Berbah'],
            ['id_kecamatan' => 2956, 'id_kabupaten_kota' => 210, 'nama_kecamatan' => 'Prambanan'],
            ['id_kecamatan' => 2957, 'id_kabupaten_kota' => 210, 'nama_kecamatan' => 'Kalasan'],
            ['id_kecamatan' => 2958, 'id_kabupaten_kota' => 210, 'nama_kecamatan' => 'Ngemplak'],
            ['id_kecamatan' => 2959, 'id_kabupaten_kota' => 210, 'nama_kecamatan' => 'Ngaglik'],
            ['id_kecamatan' => 2960, 'id_kabupaten_kota' => 210, 'nama_kecamatan' => 'Sleman'],
            ['id_kecamatan' => 2961, 'id_kabupaten_kota' => 210, 'nama_kecamatan' => 'Tempel'],
            ['id_kecamatan' => 2962, 'id_kabupaten_kota' => 210, 'nama_kecamatan' => 'Turi'],
            ['id_kecamatan' => 2963, 'id_kabupaten_kota' => 210, 'nama_kecamatan' => 'Pakem'],
            ['id_kecamatan' => 2964, 'id_kabupaten_kota' => 210, 'nama_kecamatan' => 'Cangkringan'],
            ['id_kecamatan' => 2965, 'id_kabupaten_kota' => 211, 'nama_kecamatan' => 'Srandakan'],
            ['id_kecamatan' => 2966, 'id_kabupaten_kota' => 211, 'nama_kecamatan' => 'Sanden'],
            ['id_kecamatan' => 2967, 'id_kabupaten_kota' => 211, 'nama_kecamatan' => 'Kretek'],
            ['id_kecamatan' => 2968, 'id_kabupaten_kota' => 211, 'nama_kecamatan' => 'Pundong'],
            ['id_kecamatan' => 2969, 'id_kabupaten_kota' => 211, 'nama_kecamatan' => 'Bambang Lipuro'],
            ['id_kecamatan' => 2970, 'id_kabupaten_kota' => 211, 'nama_kecamatan' => 'Pandak'],
            ['id_kecamatan' => 2971, 'id_kabupaten_kota' => 211, 'nama_kecamatan' => 'Bantul'],
            ['id_kecamatan' => 2972, 'id_kabupaten_kota' => 211, 'nama_kecamatan' => 'Jetis'],
            ['id_kecamatan' => 2973, 'id_kabupaten_kota' => 211, 'nama_kecamatan' => 'Imogiri'],
            ['id_kecamatan' => 2974, 'id_kabupaten_kota' => 211, 'nama_kecamatan' => 'Dlingo'],
            ['id_kecamatan' => 2975, 'id_kabupaten_kota' => 211, 'nama_kecamatan' => 'Pleret'],
            ['id_kecamatan' => 2976, 'id_kabupaten_kota' => 211, 'nama_kecamatan' => 'Piyungan'],
            ['id_kecamatan' => 2977, 'id_kabupaten_kota' => 211, 'nama_kecamatan' => 'Banguntapan'],
            ['id_kecamatan' => 2978, 'id_kabupaten_kota' => 211, 'nama_kecamatan' => 'Sewon'],
            ['id_kecamatan' => 2979, 'id_kabupaten_kota' => 211, 'nama_kecamatan' => 'Kasihan'],
            ['id_kecamatan' => 2980, 'id_kabupaten_kota' => 211, 'nama_kecamatan' => 'Pajangan'],
            ['id_kecamatan' => 2981, 'id_kabupaten_kota' => 211, 'nama_kecamatan' => 'Sedayu'],
            ['id_kecamatan' => 2982, 'id_kabupaten_kota' => 212, 'nama_kecamatan' => 'Mantrijeron'],
            ['id_kecamatan' => 2983, 'id_kabupaten_kota' => 212, 'nama_kecamatan' => 'Kraton'],
            ['id_kecamatan' => 2984, 'id_kabupaten_kota' => 212, 'nama_kecamatan' => 'Mergangsan'],
            ['id_kecamatan' => 2985, 'id_kabupaten_kota' => 212, 'nama_kecamatan' => 'Umbulharjo'],
            ['id_kecamatan' => 2986, 'id_kabupaten_kota' => 212, 'nama_kecamatan' => 'Kotagede'],
            ['id_kecamatan' => 2987, 'id_kabupaten_kota' => 212, 'nama_kecamatan' => 'Gondokusuman'],
            ['id_kecamatan' => 2988, 'id_kabupaten_kota' => 212, 'nama_kecamatan' => 'Danurejan'],
            ['id_kecamatan' => 2989, 'id_kabupaten_kota' => 212, 'nama_kecamatan' => 'Pakualaman'],
            ['id_kecamatan' => 2990, 'id_kabupaten_kota' => 212, 'nama_kecamatan' => 'Gondomanan'],
            ['id_kecamatan' => 2991, 'id_kabupaten_kota' => 212, 'nama_kecamatan' => 'Ngampilan'],
            ['id_kecamatan' => 2992, 'id_kabupaten_kota' => 212, 'nama_kecamatan' => 'Wirobrajan'],
            ['id_kecamatan' => 2993, 'id_kabupaten_kota' => 212, 'nama_kecamatan' => 'Gedong Tengen'],
            ['id_kecamatan' => 2994, 'id_kabupaten_kota' => 212, 'nama_kecamatan' => 'Jetis'],
            ['id_kecamatan' => 2995, 'id_kabupaten_kota' => 212, 'nama_kecamatan' => 'Tegalrejo'],
            ['id_kecamatan' => 2996, 'id_kabupaten_kota' => 213, 'nama_kecamatan' => 'Panggang'],
            ['id_kecamatan' => 2997, 'id_kabupaten_kota' => 213, 'nama_kecamatan' => 'Purwosari'],
            ['id_kecamatan' => 2998, 'id_kabupaten_kota' => 213, 'nama_kecamatan' => 'Paliyan'],
            ['id_kecamatan' => 2999, 'id_kabupaten_kota' => 213, 'nama_kecamatan' => 'Sapto Sari'],
            ['id_kecamatan' => 3000, 'id_kabupaten_kota' => 213, 'nama_kecamatan' => 'Girisuci'],
            ['id_kecamatan' => 3001, 'id_kabupaten_kota' => 213, 'nama_kecamatan' => 'Tepus'],
            ['id_kecamatan' => 3002, 'id_kabupaten_kota' => 213, 'nama_kecamatan' => 'Tanjungsari'],
            ['id_kecamatan' => 3003, 'id_kabupaten_kota' => 213, 'nama_kecamatan' => 'Rongkop'],
            ['id_kecamatan' => 3004, 'id_kabupaten_kota' => 213, 'nama_kecamatan' => 'Semin'],
            ['id_kecamatan' => 3005, 'id_kabupaten_kota' => 213, 'nama_kecamatan' => 'Ngawen'],
            ['id_kecamatan' => 3006, 'id_kabupaten_kota' => 213, 'nama_kecamatan' => 'Playen'],
            ['id_kecamatan' => 3007, 'id_kabupaten_kota' => 213, 'nama_kecamatan' => 'Patuk'],
            ['id_kecamatan' => 3008, 'id_kabupaten_kota' => 213, 'nama_kecamatan' => 'Nglipar'],
            ['id_kecamatan' => 3009, 'id_kabupaten_kota' => 213, 'nama_kecamatan' => 'Gedang Sari'],
            ['id_kecamatan' => 3010, 'id_kabupaten_kota' => 213, 'nama_kecamatan' => 'Girisubo'],
            ['id_kecamatan' => 3011, 'id_kabupaten_kota' => 213, 'nama_kecamatan' => 'Saptosari'],
            ['id_kecamatan' => 3012, 'id_kabupaten_kota' => 213, 'nama_kecamatan' => 'Karangmojo'],
            ['id_kecamatan' => 3013, 'id_kabupaten_kota' => 213, 'nama_kecamatan' => 'Wonosari'],
            ['id_kecamatan' => 3014, 'id_kabupaten_kota' => 214, 'nama_kecamatan' => 'Temon'],
            ['id_kecamatan' => 3015, 'id_kabupaten_kota' => 214, 'nama_kecamatan' => 'Wates'],
            ['id_kecamatan' => 3016, 'id_kabupaten_kota' => 214, 'nama_kecamatan' => 'Panjatan'],
            ['id_kecamatan' => 3017, 'id_kabupaten_kota' => 214, 'nama_kecamatan' => 'Galur'],
            ['id_kecamatan' => 3018, 'id_kabupaten_kota' => 214, 'nama_kecamatan' => 'Lendah'],
            ['id_kecamatan' => 3019, 'id_kabupaten_kota' => 214, 'nama_kecamatan' => 'Sentolo'],
            ['id_kecamatan' => 3020, 'id_kabupaten_kota' => 214, 'nama_kecamatan' => 'Pengasih'],
            ['id_kecamatan' => 3021, 'id_kabupaten_kota' => 214, 'nama_kecamatan' => 'Kokap'],
            ['id_kecamatan' => 3022, 'id_kabupaten_kota' => 214, 'nama_kecamatan' => 'Girimulyo'],
            ['id_kecamatan' => 3023, 'id_kabupaten_kota' => 214, 'nama_kecamatan' => 'Nanggulan'],
            ['id_kecamatan' => 3024, 'id_kabupaten_kota' => 214, 'nama_kecamatan' => 'Kalibawang'],
            ['id_kecamatan' => 3025, 'id_kabupaten_kota' => 214, 'nama_kecamatan' => 'Samigaluh']
        ];

        foreach ($kecamatans as $kecamatan) {
            Kecamatan::create($kecamatan);
        }
    }
}
