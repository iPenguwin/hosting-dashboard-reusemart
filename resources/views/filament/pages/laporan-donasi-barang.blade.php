<x-filament::page>
    <div class="space-y-6">
        {{-- 1) Pilih Tahun & Tombol Unduh PDF --}}
        <div class="flex items-center gap-4">
            <label for="tahun" class="font-medium">Tahun:</label>
            <select
                id="tahun"
                wire:model="tahun"
                class="block rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
            >
                @foreach (range(\Carbon\Carbon::now()->year, 2020) as $t)
                    <option value="{{ $t }}">{{ $t }}</option>
                @endforeach
            </select>

            <a
                href="{{ route('laporan-donasi-barang.pdf', ['tahun' => $tahun]) }}"
                target="_blank"
                class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            >
                Unduh PDF
            </a>
        </div>

        {{-- 2) Tabel --}}
        <div class="overflow-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                            Kode Produk
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                            Nama Produk
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                            ID Penitip
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                            Nama Penitip
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                            Tanggal Donasi
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                            Organisasi
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                            Nama Penerima
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($dataDonasi as $d)
                        <tr>
                            <td class="px-4 py-2">{{ $d['kode_barang'] }}</td>
                            <td class="px-4 py-2">{{ $d['nama_barang'] }}</td>
                            <td class="px-4 py-2">{{ $d['id_penitip'] }}</td>
                            <td class="px-4 py-2">{{ $d['nama_penitip'] }}</td>
                            <td class="px-4 py-2">{{ $d['tgl_donasi'] }}</td>
                            <td class="px-4 py-2">{{ $d['organisasi'] }}</td>
                            <td class="px-4 py-2">{{ $d['penerima'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-2 text-center text-gray-500 italic">
                                Tidak ada data donasi barang untuk tahun ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament::page>
