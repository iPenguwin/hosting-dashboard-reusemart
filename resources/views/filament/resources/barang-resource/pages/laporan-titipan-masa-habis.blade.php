<div>
    <x-filament::page>
        <x-filament::card>
            <div class="p-6 space-y-4">
                <div class="flex justify-between items-center flex-wrap">
                    <h2 class="text-xl font-bold">Laporan Barang Masa Penitipannya Habis</h2>
                </div>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Barang</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penitip</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Masuk</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Akhir</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batas Ambil</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($barangs as $barang)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $barang->KODE_BARANG }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $barang->NAMA_BARANG }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $barang->penitip->NAMA_PENITIP ?? '-' }}<br>
                                    <span class="text-xs text-gray-400">ID: {{ $barang->ID_PENITIP }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $barang->TGL_MASUK->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $barang->TGL_KELUAR->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $barang->TGL_KELUAR->addDays(2)->format('d/m/Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Tidak ada barang dengan masa titipan habis
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="text-xs text-gray-500 mt-2">
                    <p>Laporan ini menampilkan data barang yang masa penitipannya sudah habis (30 hari dari tanggal masuk) dan sudah melewati batas pengambilan (2 hari setelah tanggal akhir).</p>
                </div>
            </div>
        </x-filament::card>
    </x-filament::page>
</div>