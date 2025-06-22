<div>
    <x-filament::page>
        <x-filament::card>
            <div class="p-6 space-y-4">
                <div class="flex justify-between items-center flex-wrap">
                    <h2 class="text-xl font-bold">Laporan Penjualan Per Kategori</h2>
                    <div class="flex flex-col items-start space-y-1 mt-2 sm:mt-0">
                        <label for="tahun" class="text-sm text-gray-700">Tahun</label>
                        <select id="tahun" wire:model.live="tahun" class="border-gray-300 rounded text-sm px-3 py-1 w-32">
                            @foreach(range(date('Y'), date('Y') - 5) as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Item Terjual</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Item Gagal Terjual</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Item dengan Hunter</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Hunter</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($categories as $category)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $category->NAMA_KATEGORI }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->terjual }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->gagal_terjual }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->hunter }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    @if(count($category->hunter_names) > 0)
                                    {{ implode(', ', $category->hunter_names) }}
                                    @else
                                    -
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            <tr class="bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Total</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $totalTerjual }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $totalGagal }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $totalHunter }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="text-xs text-gray-500 mt-2">
                    <p>Laporan ini menampilkan data penjualan per kategori untuk tahun {{ $tahun }}.</p>
                </div>
            </div>
        </x-filament::card>
    </x-filament::page>
</div>