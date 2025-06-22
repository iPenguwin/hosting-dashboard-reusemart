<x-filament::page>
    <div class="space-y-6">
        {{-- Tombol Unduh PDF --}}
        <div>
            <a
                href="{{ route('laporan-request-donasi.pdf') }}"
                target="_blank"
                class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            >
                Unduh PDF
            </a>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto rounded-md border border-gray-200">
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">
                            ID Organisasi
                        </th>
                        <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">
                            Nama
                        </th>
                        <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">
                            Alamat
                        </th>
                        <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">
                            Request
                        </th>
                        <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">
                            Deskripsi Request
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($dataRequests as $r)
                        <tr>
                            <td class="px-2 py-2">{{ $r['id_organisasi'] }}</td>
                            <td class="px-2 py-2">{{ $r['nama'] }}</td>
                            <td class="px-2 py-2">{{ $r['alamat'] }}</td>
                            <td class="px-2 py-2">{{ $r['request'] }}</td>
                            <td class="px-2 py-2">{{ $r['deskripsi'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-2 py-2 text-center italic text-gray-500">
                                Tidak ada request donasi yang belum terpenuhi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament::page>
