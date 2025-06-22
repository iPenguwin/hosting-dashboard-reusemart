<x-filament::page>
    <div class="max-w-2xl mx-auto p-6 space-y-6">
        {{-- Judul --}}
        <h1 class="text-2xl font-bold">Laporan Transaksi Penitip</h1>

        {{-- Form GET ke route PDF --}}
        <form method="GET" action="{{ route('laporan-transaksi-penitip.pdf') }}" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                {{-- Pilih Penitip --}}
                <div>
                    <label for="penitip" class="block text-sm font-medium text-gray-700">
                        Penitip
                    </label>
                    <select
                        id="penitip"
                        name="penitip"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                    >
                        <option value="">-- Pilih Penitip --</option>
                        @foreach ($penitipOptions as $id => $nama)
                            <option value="{{ $id }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Pilih Bulan --}}
                <div>
                    <label for="bulan" class="block text-sm font-medium text-gray-700">Bulan</label>
                    <select
                        id="bulan"
                        name="bulan"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                    >
                        @php
                            $bulanOptions = [
                                1 => 'Januari',
                                2 => 'Februari',
                                3 => 'Maret',
                                4 => 'April',
                                5 => 'Mei',
                                6 => 'Juni',
                                7 => 'Juli',
                                8 => 'Agustus',
                                9 => 'September',
                                10 => 'Oktober',
                                11 => 'November',
                                12 => 'Desember',
                            ];
                            $currentMonth = now()->month;
                        @endphp

                        @foreach ($bulanOptions as $key => $label)
                            <option
                                value="{{ $key }}"
                                {{ $key === $currentMonth ? 'selected' : '' }}
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Pilih Tahun --}}
                <div>
                    <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun</label>
                    <select
                        id="tahun"
                        name="tahun"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                    >
                        @php
                            $tahunSekarang = now()->year;
                        @endphp

                        @for ($y = 2020; $y <= $tahunSekarang; $y++)
                            <option
                                value="{{ $y }}"
                                {{ $y === $tahunSekarang ? 'selected' : '' }}
                            >
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            {{-- Tombol Unduh PDF --}}
            <div class="pt-4">
                <x-filament::button type="submit">Unduh PDF</x-filament::button>
            </div>
        </form>
    </div>
</x-filament::page>
