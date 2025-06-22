<x-filament::page>
    <div class="max-w-3xl mx-auto p-6 space-y-6">
        {{-- Judul --}}
        <h1 class="text-2xl font-bold">Laporan Komisi Bulanan per Produk</h1>

        {{-- Form pilihan Bulan & Tahun --}}
        <form
            method="GET"
            action="{{ url()->current() }}"
            class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end"
        >
            {{-- Bulan --}}
            <div>
                <label for="bulan" class="block text-sm font-medium text-gray-700">Bulan</label>
                <select
                    id="bulan"
                    name="bulan"
                    class="mt-1 block w-full rounded-md border-gray-300 focus:ring-primary-500 focus:border-primary-500"
                >
                    @foreach([
                        1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
                        5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
                        9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
                    ] as $key=>$label)
                        <option value="{{ $key }}" @selected($key === $bulan)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tahun --}}
            <div>
                <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun</label>
                <select
                    id="tahun"
                    name="tahun"
                    class="mt-1 block w-full rounded-md border-gray-300 focus:ring-primary-500 focus:border-primary-500"
                >
                    @for($y = 2020; $y <= now()->year; $y++)
                        <option value="{{ $y }}" @selected($y === $tahun)>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            {{-- Tombol Tampilkan & Cetak PDF --}}
            <div class="flex gap-2">
                {{-- Refresh halaman --}}
                <x-filament::button type="submit">
                    Tampilkan
                </x-filament::button>

                {{-- Download PDF --}}
                <x-filament::button
                    type="submit"
                    formaction="{{ route('filament.laporan.komisi-bulanan-per-produk.pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                >
                    Cetak PDF
                </x-filament::button>
            </div>
        </form>

        {{-- Tabel Komisi --}}
        @if ($dataRows->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-2 py-1 border">No</th>
                            <th class="px-2 py-1 border">Kode Produk</th>
                            <th class="px-2 py-1 border text-left">Nama Produk</th>
                            <th class="px-2 py-1 border">Harga Jual</th>
                            <th class="px-2 py-1 border">Tgl Masuk</th>
                            <th class="px-2 py-1 border">Tgl Laku</th>
                            <th class="px-2 py-1 border">Komisi Hunter</th>
                            <th class="px-2 py-1 border">Komisi ReUseMart</th>
                            <th class="px-2 py-1 border">Bonus Penitip</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($dataRows as $row)
                            <tr>
                                <td class="px-2 py-1 border text-center">{{ $no++ }}</td>
                                <td class="px-2 py-1 border text-center">{{ $row['kode_produk'] }}</td>
                                <td class="px-2 py-1 border">{{ $row['nama_produk'] }}</td>
                                <td class="px-2 py-1 border text-right">{{ number_format($row['harga_jual'],0,',','.') }}</td>
                                <td class="px-2 py-1 border text-center">{{ $row['tanggal_masuk'] }}</td>
                                <td class="px-2 py-1 border text-center">{{ $row['tanggal_laku'] }}</td>
                                <td class="px-2 py-1 border text-right">{{ number_format($row['komisi_hunter'],0,',','.') }}</td>
                                <td class="px-2 py-1 border text-right">{{ number_format($row['komisi_reusemart'],0,',','.') }}</td>
                                <td class="px-2 py-1 border text-right">{{ number_format($row['bonus_penitip'],0,',','.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="bg-gray-50 font-semibold">
                            <td colspan="3" class="px-2 py-1 border text-center">Total</td>
                            <td class="px-2 py-1 border text-right">{{ number_format($dataRows->sum('harga_jual'),0,',','.') }}</td>
                            <td colspan="2" class="border"></td>
                            <td class="px-2 py-1 border text-right">{{ number_format($grandHunter,0,',','.') }}</td>
                            <td class="px-2 py-1 border text-right">{{ number_format($grandReUseMart,0,',','.') }}</td>
                            <td class="px-2 py-1 border text-right">{{ number_format($grandBonusPenitip,0,',','.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <p class="italic text-center">Tidak ada transaksi yang terjual di bulan ini.</p>
        @endif
    </div>
</x-filament::page>