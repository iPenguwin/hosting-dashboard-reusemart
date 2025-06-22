<x-filament::page>
    <div class="space-y-6">
        {{-- 1) Pilihan Tahun & Tombol Unduh PDF --}}
        <div class="flex items-center gap-4">
            <label for="tahun" class="font-medium">Tahun:</label>

            <select id="tahun"
                    wire:model="tahun"
                    class="block rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
            >
                @foreach (range(now()->year, 2020) as $y)
                    <option value="{{ $y }}" @selected($y === $tahun)>{{ $y }}</option>
                @endforeach
            </select>

            <a href="{{ route('filament.laporan.penjualan-bulanan.pdf', ['tahun' => $tahun]) }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent 
                      rounded-md font-semibold text-xs text-white uppercase tracking-widest 
                      hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 
                      focus:ring-primary-500"
               target="_blank"
            >
                Unduh PDF
            </a>
        </form>

        {{-- 2) Tabel Ringkasan --}}
        <x-filament::card class="overflow-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Bulan</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Jumlah Barang Terjual</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Jumlah Penjualan Kotor (Rp)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($dataBulanan as $d)
                        <tr>
                            <td class="px-4 py-2">{{ $d['bulan'] }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($d['jumlah_barang'], 0, ',', '.') }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($d['jumlah_penjualan'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="font-semibold bg-gray-50">
                        <td class="px-4 py-2">Total</td>
                        <td class="px-4 py-2 text-right">{{ number_format($totalBarang, 0, ',', '.') }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </x-filament::card>

        {{-- 3) Grafik Batang (Chart.js) --}}
        <div class="p-4 bg-white border border-gray-200 rounded-lg">
            <canvas id="grafikPenjualan" height="150"></canvas>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const ctx = document.getElementById('grafikPenjualan').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($dataGrafik['labels']),
                        datasets: [{
                            label: 'Total Penjualan (Rp)',
                            data: @json($dataGrafik['data']),
                            backgroundColor: '#4F46E5',
                            borderColor: '#4338CA',
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-filament::page>