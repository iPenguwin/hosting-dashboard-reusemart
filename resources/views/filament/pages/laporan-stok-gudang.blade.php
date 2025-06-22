<x-filament::page>
    <div class="max-w-4xl mx-auto p-6 space-y-6">
        {{-- Judul --}}
        <h1 class="text-3xl font-extrabold text-gray-800">Laporan Stok Gudang</h1>

        {{-- Tanggal Cetak --}}
        <p class="text-sm text-gray-600">
            Data per: <strong>{{ $tanggalCetak }}</strong>
        </p>

        {{-- Tombol Download PDF --}}
        @php
        $pdfUrl = route('filament.laporan.stok-gudang.pdf');
        @endphp
        <div class="pt-2">
            <x-filament::button
                type="button"
                class="bg-secondary-600 hover:bg-secondary-700 mb-4"
                onclick='window.location.href="{{ $pdfUrl }}"'>
                Download PDF
            </x-filament::button>
        </div>

        {{-- Tabel Stok --}}
        <x-filament::card class="overflow-x-auto bg-white shadow">
            @if($stok->isNotEmpty())
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-sm font-semibold text-gray-700">No</th>
                        <th class="px-3 py-2 text-left text-sm font-semibold text-gray-700">Kode Produk</th>
                        <th class="px-3 py-2 text-left text-sm font-semibold text-gray-700">Nama Produk</th>
                        <th class="px-3 py-2 text-left text-sm font-semibold text-gray-700">ID Penitip</th>
                        <th class="px-3 py-2 text-left text-sm font-semibold text-gray-700">Nama Penitip</th>
                        <th class="px-3 py-2 text-center text-sm font-semibold text-gray-700">Tgl Masuk</th>
                        <th class="px-3 py-2 text-center text-sm font-semibold text-gray-700">Perpanjangan</th>
                        <th class="px-3 py-2 text-left text-sm font-semibold text-gray-700">ID Hunter</th>
                        <th class="px-3 py-2 text-left text-sm font-semibold text-gray-700">Nama Hunter</th>
                        <th class="px-3 py-2 text-right text-sm font-semibold text-gray-700">Harga</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php $no = 1; @endphp
                    @foreach($stok as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 text-sm text-gray-600">{{ $no++ }}</td>
                        <td class="px-3 py-2 text-sm text-gray-600">{{ $item->KODE_BARANG }}</td>
                        <td class="px-3 py-2 text-sm text-gray-800">{{ $item->NAMA_BARANG }}</td>
                        <td class="px-3 py-2 text-sm text-gray-600">{{ $item->ID_PENITIP }}</td>
                        <td class="px-3 py-2 text-sm text-gray-600">{{ $item->NAMA_PENITIP }}</td>
                        <td class="px-3 py-2 text-sm text-gray-600 text-center">
                            {{ date('j/n/Y', strtotime($item->TGL_MASUK)) }}
                        </td>
                        <td class="px-3 py-2 text-sm text-gray-600 text-center">
                            {{ $item->perpanjangan ? 'Ya' : 'Tidak' }}
                        </td>
                        <td class="px-3 py-2 text-sm text-gray-600">{{ $item->ID_HUNTER ?? '-' }}</td>
                        <td class="px-3 py-2 text-sm text-gray-600">{{ $item->nama_hunter ?? '-' }}</td>
                        <td class="px-3 py-2 text-sm text-gray-600 text-right">
                            {{ number_format($item->harga, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="p-4 text-center text-gray-500 italic">
                Tidak ada stok untuk status “tersedia” atau “diperpanjang”.
            </div>
            @endif
        </x-filament::card>
    </div>
</x-filament::page>