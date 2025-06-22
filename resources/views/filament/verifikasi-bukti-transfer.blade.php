{{-- resources/views/filament/verifikasi-bukti-transfer.blade.php --}}
<div class="space-y-4">
    {{-- Bukti Transfer --}}
    <div class="p-4 bg-white rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900">Bukti Transfer Pembayaran</h3>

        <div class="mt-4">
            @if($buktiTransfer)
            <img
                src="{{ asset(Storage::url($buktiTransfer)) }}"
                alt="Bukti Transfer"
                class="w-full h-auto max-w-full rounded-md shadow-lg">
            @else
            <div class="p-4 text-center bg-gray-100 rounded-md">
                <p class="text-gray-500">Tidak ada bukti transfer yang diunggah</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Detail Pembayaran --}}
    <div class="p-4 bg-white rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900">Detail Pembayaran</h3>

        <div class="grid grid-cols-2 gap-4 mt-4">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Harga</p>
                <p class="text-lg font-semibold">Rp {{ number_format($totalHarga, 0, ',', '.') }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Status Verifikasi</p>
                <p class="text-lg font-semibold text-yellow-600">Menunggu Verifikasi</p>
            </div>
        </div>
    </div>

    {{-- Petunjuk Verifikasi --}}
    <div class="p-4 bg-yellow-50 rounded-lg shadow">
        <h3 class="text-lg font-medium text-yellow-800">Petunjuk Verifikasi</h3>

        <ul class="mt-2 space-y-2 text-sm text-yellow-700 list-disc list-inside">
            <li>Periksa bukti transfer dengan cermat</li>
            <li>Pastikan nominal transfer sesuai dengan total harga</li>
            <li>Verifikasi keaslian bukti transfer</li>
            <li>Jika valid, pilih "Valid" dan simpan</li>
            <li>Jika tidak valid, pilih "Tidak Valid" dan beri tahu pembeli</li>
        </ul>
    </div>
</div>