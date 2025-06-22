<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiPembelianBarangResource\Pages;
use App\Filament\Resources\TransaksiPembelianBarangResource\RelationManagers;
use App\Models\Barang;
use App\Models\Komisi;
use App\Models\Pembeli;
use App\Models\TransaksiPembelianBarang;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Date;
use Filament\Tables\Columns\DateTimeColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Facades\Auth;

class TransaksiPembelianBarangResource extends Resource
{
    protected static ?string $model = TransaksiPembelianBarang::class;

    protected static ?string $navigationLabel = 'Transaksi Pembelian Barang';

    public static ?string $label = 'Transaksi Pembelian Barang';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        $user = Auth::user();
        $isKurir = $user instanceof \App\Models\Pegawai && strtolower($user->jabatan) === 'kurir';

        if ($isKurir) {
            return $form->schema([]); // Kurir shouldn't be able to edit/create forms
        }

        return $form
            ->schema([
                Select::make('ID_PEMBELI')
                    ->label('Pembeli')
                    ->options(Pembeli::all()->pluck('NAMA_PEMBELI', 'ID_PEMBELI'))
                    ->required()
                    ->disabled(fn($operation) => $operation === 'edit')
                    ->searchable()
                    ->preload()
                    ->native(false),

                Select::make('ID_BARANG')
                    ->label('Barang')
                    ->options(fn() => Barang::where('STATUS_BARANG', 'Tersedia')->pluck('NAMA_BARANG', 'ID_BARANG'))
                    ->required()
                    ->disabled(fn($operation) => $operation === 'edit')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $barang = Barang::find($state);
                            if ($barang) {
                                $hargaBarang = $barang->HARGA_BARANG;
                                $set('TOT_HARGA_PEMBELIAN', $hargaBarang);
                                $set('HARGA_BARANG_SEBELUM_ONGKIR', $hargaBarang);
                                self::calculatePoints($set, $hargaBarang);
                            }
                        }
                    })
                    ->getOptionLabelFromRecordUsing(fn(Barang $record) => $record->NAMA_BARANG),

                FileUpload::make('BUKTI_TRANSFER')
                    ->label('Bukti Transfer')
                    ->image()
                    ->disk('public')
                    ->directory('bukti_transfer')
                    ->preserveFilenames()
                    ->columnSpanFull()
                    ->maxSize(2048)
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $set('STATUS_PEMBAYARAN', 'Sudah dibayar');
                            $set('TGL_LUNAS_PEMBELIAN', Carbon::now()->format('Y-m-d H:i:s'));
                            $set('STATUS_BUKTI_TRANSFER', 'Menunggu Verifikasi');
                            $set('STATUS_TRANSAKSI', 'Diproses');
                        } else {
                            $set('STATUS_PEMBAYARAN', 'Belum dibayar');
                            $set('TGL_LUNAS_PEMBELIAN', null);
                            $set('STATUS_BUKTI_TRANSFER', 'Tidak Valid');
                        }
                        self::updateTransactionStatus($set);
                    }),

                DatePicker::make('TGL_AMBIL_KIRIM')
                    ->label('Tanggal Ambil/Kirim')
                    ->placeholder('Pilih Tanggal Ambil/Kirim')
                    ->format('Y-m-d H:i:s')
                    ->seconds(false)
                    ->native(false)
                    ->minutesStep(15)
                    ->rules([
                        function ($get) {
                            return function (string $attribute, $value, $fail) use ($get) {
                                $selectedDate = Carbon::parse($value);
                                $now = Carbon::now();

                                if (
                                    $get('DELIVERY_METHOD') === 'Di Kirim' &&
                                    $now->hour >= 16 &&
                                    $selectedDate->isSameDay($now)
                                ) {
                                    $fail('Pengiriman di atas jam 4 sore tidak bisa dijadwalkan untuk hari yang sama.');
                                }
                            };
                        },
                    ])
                    ->helperText(function ($get) {
                        $now = Carbon::now();
                        if ($get('DELIVERY_METHOD') === 'Di Kirim' && $now->hour >= 16) {
                            return 'Pengiriman di atas jam 4 sore akan dijadwalkan untuk hari berikutnya. Notifikasi akan dikirim ke pembeli, penitip, dan kurir.';
                        }
                        return null;
                    }),

                DatePicker::make('TGL_LUNAS_PEMBELIAN')
                    ->label('Tanggal Lunas Pembelian')
                    ->displayFormat('l, j F Y')
                    ->maxDate(now())
                    ->native(false)
                    ->reactive()
                    ->disabled(fn($get) => $get('STATUS_PEMBAYARAN') !== 'Sudah dibayar')
                    ->dehydrated(),

                DatePicker::make('TGL_PESAN_PEMBELIAN')
                    ->label('Tanggal Pesan Pembelian')
                    ->displayFormat('d/m/Y H:i')
                    ->native(false)
                    ->seconds(false)
                    ->default(Carbon::now())
                    ->minutesStep(15)
                    ->disabled(fn($operation) => $operation === 'edit')
                    ->required(),

                TextInput::make('TOT_HARGA_PEMBELIAN')
                    ->label('Total Harga Pembelian (Rp)')
                    ->required()
                    ->numeric()
                    ->disabled()
                    ->dehydrated()
                    ->default(0)
                    ->placeholder('Harga akan otomatis terisi'),

                TextInput::make('HARGA_BARANG_SEBELUM_ONGKIR')
                    ->label('Harga Barang Sebelum Ongkir (Rp)')
                    ->required()
                    ->numeric()
                    ->disabled()
                    ->dehydrated()
                    ->default(0)
                    ->hidden(),

                Select::make('STATUS_PEMBAYARAN')
                    ->required()
                    ->label('Status Pembayaran')
                    ->default('Belum dibayar')
                    ->placeholder('Masukkan Status Pembayaran')
                    ->options([
                        'Sudah dibayar' => 'Sudah dibayar',
                        'Belum dibayar' => 'Belum dibayar',
                    ])
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state === 'Sudah dibayar') {
                            $set('STATUS_BUKTI_TRANSFER', 'Menunggu Verifikasi');
                            $set('TGL_LUNAS_PEMBELIAN', Carbon::now()->format('Y-m-d'));
                        } else {
                            $set('STATUS_BUKTI_TRANSFER', '-');
                            $set('TGL_LUNAS_PEMBELIAN', null);
                        }
                        self::updateTransactionStatus($set);
                    }),

                Select::make('DELIVERY_METHOD')
                    ->required()
                    ->label('Metode Pengiriman')
                    ->placeholder('Masukkan Metode Pengiriman')
                    ->options([
                        'Ambil Sendiri' => 'Ambil Sendiri',
                        'Di Kirim' => 'Di Kirim',
                    ])
                    ->live()
                    ->disabled(fn($operation) => $operation === 'edit')
                    ->afterStateUpdated(function ($state, Forms\Set $set, $get) {
                        $hargaBarangAsli = $get('HARGA_BARANG_SEBELUM_ONGKIR') ?? $get('TOT_HARGA_PEMBELIAN') ?? 0;
                        $ongkir = 0;
                        $potongan = 0;

                        if ($state === 'Di Kirim') {
                            if ($hargaBarangAsli >= 1500000) {
                                $ongkir = 0;
                                $potongan = 0;
                            } else {
                                $ongkir = 100000;
                                $potongan = 0;
                            }
                        }

                        $set('ONGKOS_KIRIM', $ongkir);
                        $set('POTONGAN_GRATIS_ONGKIR', $potongan);

                        $totalHarga = $hargaBarangAsli + $ongkir - $potongan;
                        $set('TOT_HARGA_PEMBELIAN', max($totalHarga, 0));

                        self::updateTransactionStatus($set);
                    }),

                TextInput::make('ONGKOS_KIRIM')
                    ->label('Ongkos Kirim (Rp)')
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->placeholder('Ongkos kirim akan otomatis terisi')
                    ->hidden(fn($operation) => $operation === 'edit'),

                TextInput::make('POTONGAN_GRATIS_ONGKIR')
                    ->label('Potongan Gratis Ongkir (Rp)')
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->hidden(),
                TextInput::make('POIN_DIDAPAT')
                    ->label('Poin Didapat')
                    ->required()
                    ->disabled()
                    ->dehydrated(true)
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->placeholder('Poin akan otomatis terhitung')
                    ->hidden(fn($operation) => $operation === 'edit'),
                TextInput::make('POIN_POTONGAN')
                    ->label('Poin Potongan')
                    ->dehydrated()
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->placeholder('Masukkan Poin Potongan')
                    ->columnSpanFull()
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set) {
                        self::updateTransactionStatus($set);
                    }),

                Select::make('STATUS_BUKTI_TRANSFER')
                    ->required()
                    ->disabled()
                    ->dehydrated()
                    ->label('Status Bukti Transfer')
                    ->default('N/A')
                    ->placeholder('Status Transfer')
                    ->options([
                        'N/A' => 'Status Transfer',
                        'Valid' => 'Valid',
                        'Menunggu Verifikasi' => 'Menunggu Verifikasi',
                        'Tidak Valid' => 'Tidak Valid',
                    ])
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set, $get) {
                        if ($get('STATUS_PEMBAYARAN') === 'Sudah dibayar' && $state === 'Menunggu Verifikasi') {
                            $set('STATUS_TRANSAKSI', 'Diproses');
                        }
                        self::updateTransactionStatus($set);
                    }),

                Select::make('STATUS_TRANSAKSI')
                    ->required()
                    ->disabled()
                    ->dehydrated()
                    ->default('Menunggu Pembayaran')
                    ->label('Status Transaksi')
                    ->placeholder('Pilih Status Transaksi')
                    ->options([
                        'Hangus' => 'Hangus',
                        'Menunggu Pembayaran' => 'Menunggu Pembayaran',
                        'Perlu Dikirim' => 'Perlu Dikirim',
                        'Dikirim' => 'Dikirim',
                        'Diproses' => 'Diproses',
                        'Perlu Diambil' => 'Perlu Diambil',
                        'Selesai' => 'Selesai',
                    ]),
            ]);
    }

    protected static function calculatePoints(Forms\Set $set, $hargaBarang): void
    {
        $basePoints = floor($hargaBarang / 10000);
        $bonusPoints = $hargaBarang > 500000 ? floor($basePoints * 0.2) : 0;
        $totalPoints = $basePoints + $bonusPoints;
        $set('POIN_DIDAPAT', $totalPoints);
    }

    protected static function updateTransactionStatus(Forms\Set $set): void
    {
        $set('STATUS_TRANSAKSI', function ($get) {
            $statusPembayaran = $get('STATUS_PEMBAYARAN');
            $statusBuktiTransfer = $get('STATUS_BUKTI_TRANSFER');
            $buktiTransfer = $get('BUKTI_TRANSFER');
            $deliveryMethod = $get('DELIVERY_METHOD');

            // Jika belum ada bukti transfer dan status pembayaran masih Belum dibayar
            if (empty($buktiTransfer)) {
                if ($statusPembayaran === 'Belum dibayar' && $statusBuktiTransfer === 'Diproses') {
                    return 'Menunggu Pembayaran';
                }
            }

            // Jika sudah ada bukti transfer dan status pembayaran Lunas
            if ($statusPembayaran === 'Sudah dibayar' && $statusBuktiTransfer === 'Valid') {
                if ($deliveryMethod === 'Ambil Sendiri') {
                    return 'Perlu Diambil';
                } else {
                    return 'Perlu Dikirim';
                }
            }

            // Jika bukti transfer tidak valid
            if ($statusBuktiTransfer === 'Tidak Valid') {
                return 'Menunggu Pembayaran';
            }

            // Default kembalikan status yang sudah ada atau 'Menunggu Pembayaran'
            return $get('STATUS_TRANSAKSI') ?? 'Menunggu Pembayaran';
        });
    }

    public static function table(Table $table): Table
    {
        $user = Auth::user();
        $isKurir = $user instanceof \App\Models\Pegawai && strtolower($user->jabatan) === 'kurir';

        if ($isKurir) {
            return $table
                ->columns([
                    TextColumn::make('ID_TRANSAKSI_PEMBELIAN')
                        ->label('ID Transaksi')
                        ->sortable()
                        ->searchable(),

                    TextColumn::make('pembeli.NAMA_PEMBELI')
                        ->label('Pembeli')
                        ->sortable()
                        ->searchable(),

                    TextColumn::make('barang.NAMA_BARANG')
                        ->label('Barang')
                        ->sortable()
                        ->searchable(),

                    TextColumn::make('TOT_HARGA_PEMBELIAN')
                        ->label('Total Harga')
                        ->numeric()
                        ->sortable()
                        ->money('IDR'),

                    TextColumn::make('ONGKOS_KIRIM')
                        ->label('Ongkir')
                        ->numeric()
                        ->sortable()
                        ->money('IDR'),

                    TextColumn::make('STATUS_BUKTI_TRANSFER')
                        ->label('Status Bukti Transfer')
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'Valid' => 'success',
                            'Menunggu Verifikasi' => 'warning',
                            'Tidak Valid' => 'danger',
                            default => 'gray',
                        })
                        ->sortable(),
                ])
                ->filters([
                    Tables\Filters\Filter::make('Perlu Dikirim')
                        ->label('Barang Perlu Dikirim')
                        ->query(function ($query) {
                            return $query
                                ->where('DELIVERY_METHOD', 'Di Kirim')
                                ->where('STATUS_PEMBAYARAN', 'Sudah dibayar')
                                ->where('STATUS_BUKTI_TRANSFER', 'Valid')
                                ->where('STATUS_TRANSAKSI', 'Perlu Dikirim');
                        }),
                ])
                ->actions([
                    Tables\Actions\Action::make('printNotaKurir')
                        ->label('Nota Kurir')
                        ->icon('heroicon-o-truck') // Different icon for distinction
                        ->color('success') // Different color
                        ->visible(function (TransaksiPembelianBarang $record): bool {
                            // Optionally, make this visible only if DELIVERY_METHOD is 'Di Kirim'
                            return in_array($record->STATUS_TRANSAKSI, ['Transaksi Berhasil', 'Diproses', 'Selesai']) &&
                                $record->DELIVERY_METHOD === 'Di Kirim'; // More specific visibility
                        })
                        ->action(function (TransaksiPembelianBarang $record) {
                            $transaksi = $record->load([
                                'pembeli.alamats',
                                'barang',
                                'pegawaiTransaksiPembelians.pegawai.jabatans'
                            ]);

                            $qcPegawai = $transaksi->pegawaiTransaksiPembelians->first(function ($ptp) {
                                return optional($ptp->pegawai->jabatans)->contains('NAMA_JABATAN', 'Pegawai Gudang');
                            });

                            $tanggalPesan = Carbon::parse($transaksi->TGL_PESAN_PEMBELIAN);
                            $tanggalLunas = $transaksi->TGL_LUNAS_PEMBELIAN ? Carbon::parse($transaksi->TGL_LUNAS_PEMBELIAN) : null;
                            $tanggalAmbil = $transaksi->TGL_AMBIL_KIRIM ? Carbon::parse($transaksi->TGL_AMBIL_KIRIM) : null;

                            // Prepare data for the PDF view (same structure)
                            $data = [
                                'no_nota' => 'INV' . str_pad($transaksi->ID_TRANSAKSI_PEMBELIAN, 6, '0', STR_PAD_LEFT),
                                'tanggal_pesan' => $tanggalPesan->format('d/m/Y H:i'),
                                'tanggal_lunas' => $tanggalLunas?->format('d/m/Y H:i') ?? '-',
                                'tanggal_ambil' => $tanggalAmbil?->format('d/m/Y H:i') ?? '-',
                                'pembeli' => [
                                    'email' => $transaksi->pembeli->EMAIL_PEMBELI,
                                    'nama' => $transaksi->pembeli->NAMA_PEMBELI,
                                    'alamat' => optional($transaksi->pembeli->alamats()->first())->ALAMAT_LENGKAP ?? '-',
                                ],
                                'delivery_method' => $transaksi->DELIVERY_METHOD,
                                'items' => [
                                    [
                                        'nama' => $transaksi->barang->NAMA_BARANG,
                                        'harga' => number_format($transaksi->barang->HARGA_BARANG, 0, ',', '.'),
                                    ]
                                ],
                                'ongkos_kirim' => number_format($transaksi->ONGKOS_KIRIM, 0, ',', '.'),
                                'potongan_poin' => $transaksi->POIN_POTONGAN > 0
                                    ? $transaksi->POIN_POTONGAN . ' poin - ' . number_format($transaksi->POIN_POTONGAN * 100, 0, ',', '.')
                                    : '-',
                                'poin_didapat' => $transaksi->POIN_DIDAPAT,
                                'total_poin' => $transaksi->pembeli->POINT_LOYALITAS_PEMBELI ?? 0,
                                'qc_oleh' => $qcPegawai ?
                                    $qcPegawai->pegawai->NAMA_PEGAWAI . ' (' . $qcPegawai->pegawai->KODE_PEGAWAI . ')' :
                                    'Belum ditentukan',
                                // Pass the raw transaction object if the blade needs more complex data like $transaksi->POIN_POTONGAN
                                'transaksi_raw_poin_potongan' => $transaksi->POIN_POTONGAN,
                                // 'nama_kurir' => 'Cahyono', // Example if you have a way to fetch this
                            ];

                            // Load the specific Blade view for courier
                            $pdf = Pdf::loadView('pdf.nota_penjualan_kurir', $data);
                            return response()->streamDownload(
                                fn() => print($pdf->output()), // Use output() for streamDownload
                                'nota-kurir-' . $data['no_nota'] . '.pdf'
                            );
                        }),
                    // Tables\Actions\Action::make('markAsShipped')
                    //     ->label('Konfirmasi Barang Terkirim')
                    //     ->icon('heroicon-o-check-circle')
                    //     ->color('success')
                    //     ->visible(function (TransaksiPembelianBarang $record): bool {
                    //         return $record->STATUS_PEMBAYARAN == 'Sudah dibayar' &&
                    //             !empty($record->BUKTI_TRANSFER) &&
                    //             $record->STATUS_BUKTI_TRANSFER == 'Valid' &&
                    //             $record->DELIVERY_METHOD == 'Di Kirim' &&
                    //             $record->STATUS_TRANSAKSI == 'Dikirim';
                    //     })
                    //     ->action(function (TransaksiPembelianBarang $record) {
                    //         $record->update([
                    //             'STATUS_TRANSAKSI' => 'Selesai',
                    //             'TGL_AMBIL_KIRIM' => Carbon::now(),
                    //         ]);

                    //         // Update item status
                    //         $record->barang->update([
                    //             'STATUS_BARANG' => 'Terkirim'
                    //         ]);

                    //         Notification::make()
                    //             ->title('Status transaksi diperbarui: Dikirim')
                    //             ->success()
                    //             ->send();
                    //     })
                    //     ->requiresConfirmation()
                    //     ->modalHeading('Konfirmasi Pengiriman')
                    //     ->modalDescription('Apakah Anda yakin ingin menandai transaksi ini sebagai "Dikirim"? Status barang akan diubah menjadi "Terkirim".')
                    //     ->modalSubmitActionLabel('Ya, Kirimkan'),

                    Tables\Actions\Action::make('markAsShipped')
                        ->label('Konfirmasi Barang Terkirim')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(function (TransaksiPembelianBarang $record): bool {
                            return $record->STATUS_PEMBAYARAN == 'Sudah dibayar' &&
                                !empty($record->BUKTI_TRANSFER) &&
                                $record->STATUS_BUKTI_TRANSFER == 'Valid' &&
                                $record->DELIVERY_METHOD == 'Di Kirim' &&
                                $record->STATUS_TRANSAKSI == 'Dikirim';
                        })
                        ->action(function (TransaksiPembelianBarang $record) {
                            $user = Auth::user();
                            // if ($user instanceof \App\Models\Pegawai && strtolower($user->jabatan) === 'Kurir') {
                            $record->confirmShipmentByKurir($user->ID_PEGAWAI);
                            // } else {
                            //     Notification::make()
                            //         ->title('Hanya kurir yang dapat mengonfirmasi pengiriman')
                            //         ->danger()
                            //         ->send();
                            // }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Pengiriman')
                        ->modalDescription('Apakah Anda yakin ingin menandai transaksi ini sebagai "Selesai"? Status barang akan diubah menjadi "Terkirim".')
                        ->modalSubmitActionLabel('Ya, Konfirmasi'),

                    Tables\Actions\Action::make('markAsReadyToShip')
                        ->label('Tandai Perlu Dikirim')
                        ->icon('heroicon-o-truck')
                        ->color('success')
                        ->visible(function (TransaksiPembelianBarang $record): bool {
                            return $record->STATUS_PEMBAYARAN == 'Sudah dibayar' &&
                                !empty($record->BUKTI_TRANSFER) &&
                                $record->STATUS_BUKTI_TRANSFER == 'Valid' &&
                                $record->DELIVERY_METHOD == 'Di Kirim' &&
                                $record->STATUS_TRANSAKSI == 'Perlu Dikirim';
                        })
                        ->action(function (TransaksiPembelianBarang $record) {
                            $record->update([
                                'STATUS_TRANSAKSI' => 'Dikirim'
                            ]);

                            // Update item status
                            $record->barang->update([
                                'STATUS_BARANG' => 'Dalam Pengiriman'
                            ]);

                            Notification::make()
                                ->title('Dalam proses pengiriman: Barang dalam perjalanan')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Perlu Dikirim')
                        ->modalDescription('Apakah Anda yakin ingin menandai transaksi ini sebagai "Perlu Dikirim"? Status barang akan diubah menjadi "Dalam Pengiriman".')
                        ->modalSubmitActionLabel('Ya, Perlu Dikirim'),
                ])
                ->bulkActions([])
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->where('DELIVERY_METHOD', 'Di Kirim')
                        ->where('STATUS_PEMBAYARAN', 'Sudah dibayar')
                        ->where('STATUS_BUKTI_TRANSFER', 'Valid')
                        ->whereIn('STATUS_TRANSAKSI', ['Perlu Dikirim', 'Dikirim', 'Selesai']);
                });
        }

        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('barang.STATUS_BARANG')
                    ->label('Status Barang')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'Tersedia' => 'success',
                        'Dipesan' => 'warning',
                        'Terjual' => 'danger',
                        'Tidak Terjual' => 'gray',
                        'Menunggu Diambil' => 'warning',
                        'Diambil' => 'success',
                        'Menunggu Dikirim' => 'warning',
                        'Dalam Pengiriman' => 'warning',
                        'Terkirim' => 'success',
                        'Didonasikan' => 'success',
                        'Diperpanjang' => 'primary',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('ID_TRANSAKSI_PEMBELIAN')
                    ->label('ID Transaksi')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('pembeli.NAMA_PEMBELI')
                    ->label('Pembeli')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('barang.NAMA_BARANG')
                    ->label('Barang')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('TOT_HARGA_PEMBELIAN')
                    ->label('Total Harga')
                    ->numeric()
                    ->sortable()
                    ->money('IDR'),

                TextColumn::make('ONGKOS_KIRIM')
                    ->label('Ongkir')
                    ->numeric()
                    ->sortable()
                    ->money('IDR'),

                TextColumn::make('POIN_DIDAPAT')
                    ->label('Poin Didapat')
                    ->sortable()
                    ->default(0),

                TextColumn::make('POIN_POTONGAN')
                    ->label('Poin Potongan')
                    ->sortable(),

                TextColumn::make('STATUS_BUKTI_TRANSFER')
                    ->label('Status Bukti Transfer')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Valid' => 'success',
                        'Menunggu Verifikasi' => 'warning',
                        'Tidak Valid' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('STATUS_PEMBAYARAN')
                    ->badge()
                    ->label('Status Pembayaran')
                    ->color(fn(string $state): string => match ($state) {
                        'Sudah dibayar' => 'success',
                        'Belum dibayar' => 'danger',
                        'Diproses' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('STATUS_TRANSAKSI')
                    ->badge()
                    ->label('Status Transaksi')
                    ->color(fn(string $state): string => match ($state) {
                        'Hangus' => 'danger',
                        'Menunggu Pembayaran' => 'warning',
                        'Perlu Dikirim' => 'warning',
                        'Dikirim' => 'warning',
                        'Diproses' => 'warning',
                        'Perlu Diambil' => 'warning',
                        'Selesai' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('TGL_PESAN_PEMBELIAN')
                    ->label('Tanggal Pesan')
                    ->dateTime('d/m/Y')
                    ->sortable(),

                TextColumn::make('TGL_LUNAS_PEMBELIAN')
                    ->label('Tanggal Lunas')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('TGL_AMBIL_KIRIM')
                    ->label('Tanggal Ambil / Kirim')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('nama_pegawai_kurir')
                    ->label('Kurir Pengirim')
                    ->sortable()
                    ->searchable()
                    ->default('-'),
            ])
            ->poll('10s')
            ->filters([
                Tables\Filters\Filter::make('Menunggu Diambil')
                    ->label('Barang Siap Diambil')
                    ->query(function ($query) {
                        return $query
                            ->where('DELIVERY_METHOD', 'Ambil Sendiri')
                            ->where('STATUS_PEMBAYARAN', 'Sudah dibayar')
                            ->whereHas('barang', function ($q) {
                                $q->where('STATUS_BARANG', 'Menunggu Diambil');
                            });
                    }),
                Tables\Filters\SelectFilter::make('STATUS_PEMBAYARAN')
                    ->options([
                        'Sudah dibayar' => 'Sudah dibayar',
                        'Belum dibayar' => 'Belum dibayar',
                    ])
                    ->label('Status Pembayaran'),

                Tables\Filters\SelectFilter::make('STATUS_TRANSAKSI')
                    ->options([
                        'Transaksi Berhasil' => 'Transaksi Berhasil',
                        'Transaksi Selesai' => 'Transaksi Selesai',
                        'Diproses' => 'Diproses',
                        'Hangus' => 'Hangus',
                    ])
                    ->label('Status Transaksi'),
            ])
            ->actions([
                Tables\Actions\Action::make('printNotaAmbilSendiri')
                    ->label('Nota Pembeli')
                    ->icon('heroicon-o-document-text') // Different icon for distinction
                    ->color('info') // Different color
                    ->visible(function (TransaksiPembelianBarang $record): bool {
                        // Optionally, make this visible only if DELIVERY_METHOD is 'Ambil Sendiri'
                        // For now, keeping the original visibility condition
                        return in_array($record->STATUS_TRANSAKSI, ['Selesai']) &&
                            $record->DELIVERY_METHOD === 'Ambil Sendiri' &&
                            $record->STATUS_BUKTI_TRANSFER === 'Valid';
                    })
                    ->action(function (TransaksiPembelianBarang $record) {
                        $transaksi = $record->load([
                            'pembeli.alamats', // Eager load alamats for the pembeli
                            'barang',
                            'pegawaiTransaksiPembelians.pegawai'
                        ]);

                        $tanggalPesan = Carbon::parse($transaksi->TGL_PESAN_PEMBELIAN);
                        $tanggalLunas = $transaksi->TGL_LUNAS_PEMBELIAN ? Carbon::parse($transaksi->TGL_LUNAS_PEMBELIAN) : null;
                        $tanggalAmbil = $transaksi->TGL_AMBIL_KIRIM ? Carbon::parse($transaksi->TGL_AMBIL_KIRIM) : null;

                        // Prepare data for the PDF view
                        $data = [
                            'no_nota' => 'INV' . str_pad($transaksi->ID_TRANSAKSI_PEMBELIAN, 6, '0', STR_PAD_LEFT),
                            'tanggal_pesan' => $tanggalPesan->format('d/m/Y H:i'),
                            'tanggal_lunas' => $tanggalLunas?->format('d/m/Y H:i') ?? '-',
                            'tanggal_ambil' => $tanggalAmbil?->format('d/m/Y H:i') ?? '-', // Label handled in Blade
                            'pembeli' => [
                                'email' => $transaksi->pembeli->EMAIL_PEMBELI,
                                'nama' => $transaksi->pembeli->NAMA_PEMBELI,
                                // Ensure alamats relationship is correctly defined in Pembeli model
                                'alamat' => optional($transaksi->pembeli->alamats()->first())->ALAMAT_LENGKAP ?? '-',
                            ],
                            'delivery_method' => $transaksi->DELIVERY_METHOD,
                            'items' => [ // Assuming single item transaction as per your original data structure
                                [
                                    'nama' => $transaksi->barang->NAMA_BARANG,
                                    'harga' => number_format($transaksi->barang->HARGA_BARANG, 0, ',', '.'),
                                ]
                            ],
                            // 'total_harga' is used in the blade for (sum_items + ongkir) before point deduction.
                            // Your original code for total_harga and total_bayar was the same.
                            // The blade @php block handles the multi-step total calculation.
                            'ongkos_kirim' => number_format($transaksi->ONGKOS_KIRIM, 0, ',', '.'),
                            'potongan_poin' => $transaksi->POIN_POTONGAN > 0
                                ? $transaksi->POIN_POTONGAN . ' poin - ' . number_format($transaksi->POIN_POTONGAN * 100, 0, ',', '.')
                                : '-',
                            'poin_didapat' => $transaksi->POIN_DIDAPAT,
                            'total_poin' => $transaksi->pembeli->POINT_LOYALITAS_PEMBELI ?? 0,
                            'transaksi' => $transaksi,
                            'qc_oleh' => (
                                $transaksi->pegawaiTransaksiPembelians->first() && $transaksi->pegawaiTransaksiPembelians->first()->pegawai
                                ? $transaksi->pegawaiTransaksiPembelians->first()->pegawai->NAMA_PEGAWAI . ' (' . $transaksi->pegawaiTransaksiPembelians->first()->pegawai->ID_PEGAWAI . ')'
                                : '-'
                            ),
                            // Pass the raw transaction object if the blade needs more complex data like $transaksi->POIN_POTONGAN
                            'transaksi_raw_poin_potongan' => $transaksi->POIN_POTONGAN,
                        ];

                        // Load the specific Blade view for self-pickup
                        $pdf = Pdf::loadView('pdf.nota_penjualan_ambil_sendiri', $data);
                        return response()->streamDownload(
                            fn() => print($pdf->output()), // Use output() for streamDownload
                            'nota-ambil-' . $data['no_nota'] . '.pdf'
                        );
                    }),

                Tables\Actions\Action::make('printNotaKurir')
                    ->label('Nota Kurir')
                    ->icon('heroicon-o-truck') // Different icon for distinction
                    ->color('success') // Different color
                    ->visible(function (TransaksiPembelianBarang $record): bool {
                        // Optionally, make this visible only if DELIVERY_METHOD is 'Di Kirim'
                        return in_array($record->STATUS_TRANSAKSI, ['Transaksi Berhasil', 'Diproses', 'Selesai']) &&
                            $record->DELIVERY_METHOD === 'Di Kirim'; // More specific visibility
                    })
                    ->action(function (TransaksiPembelianBarang $record) {
                        $transaksi = $record->load([
                            'pembeli.alamats',
                            'barang',
                            'pegawaiTransaksiPembelians.pegawai.jabatans'
                        ]);

                        $qcPegawai = $transaksi->pegawaiTransaksiPembelians->first(function ($ptp) {
                            return optional($ptp->pegawai->jabatans)->contains('NAMA_JABATAN', 'Pegawai Gudang');
                        });

                        $tanggalPesan = Carbon::parse($transaksi->TGL_PESAN_PEMBELIAN);
                        $tanggalLunas = $transaksi->TGL_LUNAS_PEMBELIAN ? Carbon::parse($transaksi->TGL_LUNAS_PEMBELIAN) : null;
                        $tanggalAmbil = $transaksi->TGL_AMBIL_KIRIM ? Carbon::parse($transaksi->TGL_AMBIL_KIRIM) : null;

                        // Prepare data for the PDF view (same structure)
                        $data = [
                            'no_nota' => 'INV' . str_pad($transaksi->ID_TRANSAKSI_PEMBELIAN, 6, '0', STR_PAD_LEFT),
                            'tanggal_pesan' => $tanggalPesan->format('d/m/Y H:i'),
                            'tanggal_lunas' => $tanggalLunas?->format('d/m/Y H:i') ?? '-',
                            'tanggal_ambil' => $tanggalAmbil?->format('d/m/Y H:i') ?? '-',
                            'pembeli' => [
                                'email' => $transaksi->pembeli->EMAIL_PEMBELI,
                                'nama' => $transaksi->pembeli->NAMA_PEMBELI,
                                'alamat' => optional($transaksi->pembeli->alamats()->first())->ALAMAT_LENGKAP ?? '-',
                            ],
                            'delivery_method' => $transaksi->DELIVERY_METHOD,
                            'items' => [
                                [
                                    'nama' => $transaksi->barang->NAMA_BARANG,
                                    'harga' => number_format($transaksi->barang->HARGA_BARANG, 0, ',', '.'),
                                ]
                            ],
                            'ongkos_kirim' => number_format($transaksi->ONGKOS_KIRIM, 0, ',', '.'),
                            'potongan_poin' => $transaksi->POIN_POTONGAN > 0
                                ? $transaksi->POIN_POTONGAN . ' poin - ' . number_format($transaksi->POIN_POTONGAN * 100, 0, ',', '.')
                                : '-',
                            'poin_didapat' => $transaksi->POIN_DIDAPAT,
                            'total_poin' => $transaksi->pembeli->POINT_LOYALITAS_PEMBELI ?? 0,
                            'qc_oleh' => $qcPegawai ?
                                $qcPegawai->pegawai->NAMA_PEGAWAI . ' (' . $qcPegawai->pegawai->KODE_PEGAWAI . ')' :
                                'Belum ditentukan',
                            // Pass the raw transaction object if the blade needs more complex data like $transaksi->POIN_POTONGAN
                            'transaksi_raw_poin_potongan' => $transaksi->POIN_POTONGAN,
                            // 'nama_kurir' => 'Cahyono', // Example if you have a way to fetch this
                        ];

                        // Load the specific Blade view for courier
                        $pdf = Pdf::loadView('pdf.nota_penjualan_kurir', $data);
                        return response()->streamDownload(
                            fn() => print($pdf->output()), // Use output() for streamDownload
                            'nota-kurir-' . $data['no_nota'] . '.pdf'
                        );
                    }),

                Tables\Actions\Action::make('markAsReadyToShip')
                    ->label('Tandai Perlu Dikirim')
                    ->icon('heroicon-o-truck')
                    ->color('success')
                    ->visible(function (TransaksiPembelianBarang $record): bool {
                        return $record->STATUS_PEMBAYARAN == 'Sudah dibayar' &&
                            !empty($record->BUKTI_TRANSFER) &&
                            $record->STATUS_BUKTI_TRANSFER == 'Valid' &&
                            $record->DELIVERY_METHOD == 'Di Kirim' &&
                            $record->STATUS_TRANSAKSI == 'Perlu Dikirim';
                    })
                    ->action(function (TransaksiPembelianBarang $record) {
                        $record->update([
                            'STATUS_TRANSAKSI' => 'Dikirim'
                        ]);

                        // Update item status
                        $record->barang->update([
                            'STATUS_BARANG' => 'Dalam Pengiriman'
                        ]);

                        Notification::make()
                            ->title('Dalam proses pengiriman: Barang dalam perjalanan')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Perlu Dikirim')
                    ->modalDescription('Apakah Anda yakin ingin menandai transaksi ini sebagai "Perlu Dikirim"? Status barang akan diubah menjadi "Dalam Pengiriman".')
                    ->modalSubmitActionLabel('Ya, Perlu Dikirim'),

                Tables\Actions\Action::make('verifyPayment')
                    ->label('Verifikasi Pembayaran')
                    ->icon('heroicon-o-check-circle')
                    ->color('warning')
                    ->visible(
                        fn(TransaksiPembelianBarang $record): bool =>
                        $record->STATUS_BUKTI_TRANSFER === 'Menunggu Verifikasi'
                    )
                    ->modalHeading('Verifikasi Bukti Transfer')
                    ->modalSubmitActionLabel('Simpan Verifikasi')
                    ->modalContent(function (TransaksiPembelianBarang $record) {
                        return view('filament.verifikasi-bukti-transfer', [
                            'buktiTransfer' => $record->BUKTI_TRANSFER,
                            'totalHarga' => $record->TOT_HARGA_PEMBELIAN,
                        ]);
                    })
                    ->form([
                        Forms\Components\Radio::make('verification_status')
                            ->label('Status Verifikasi')
                            ->options([
                                'valid' => 'Valid',
                                'invalid' => 'Tidak Valid',
                            ])
                            ->required()
                            ->default('valid'),
                    ])
                    ->action(function (TransaksiPembelianBarang $record, array $data) {
                        $status = $data['verification_status'] === 'valid' ? 'Valid' : 'Tidak Valid';

                        $updateData = [
                            'STATUS_BUKTI_TRANSFER' => $status,
                            'TGL_LUNAS_PEMBELIAN' => $status === 'Valid' ? Carbon::now()->format('Y-m-d') : null,
                        ];

                        if ($status === 'Valid') {
                            $updateData['STATUS_PEMBAYARAN'] = 'Sudah dibayar';
                            $updateData['STATUS_TRANSAKSI'] = $record->DELIVERY_METHOD === 'Ambil Sendiri'
                                ? 'Perlu Diambil'
                                : 'Perlu Dikirim';

                            $record->barang->update([
                                'STATUS_BARANG' => $record->DELIVERY_METHOD === 'Ambil Sendiri'
                                    ? 'Menunggu Diambil'
                                    : 'Menunggu Dikirim'
                            ]);
                        } else {
                            $updateData['STATUS_PEMBAYARAN'] = 'Belum dibayar';
                            $updateData['STATUS_TRANSAKSI'] = 'Hangus';
                            if ($record->barang) {
                                $record->barang->update([
                                    'STATUS_BARANG' => 'Tersedia'
                                ]);
                            }
                        }

                        $record->update($updateData);

                        Notification::make()
                            ->title($status === 'Valid'
                                ? 'Bukti transfer telah divalidasi'
                                : 'Bukti transfer ditandai sebagai tidak valid - Status transaksi diubah menjadi Hangus dan barang kembali tersedia')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('confirmPickup')
                    ->label('Konfirmasi Pengambilan')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(function (TransaksiPembelianBarang $record): bool {
                        return $record->STATUS_PEMBAYARAN == 'Sudah dibayar' &&
                            !empty($record->BUKTI_TRANSFER) &&
                            $record->STATUS_BUKTI_TRANSFER == 'Valid' &&
                            $record->DELIVERY_METHOD == 'Ambil Sendiri' &&
                            $record->STATUS_TRANSAKSI == 'Perlu Diambil';
                    })
                    ->action(function (TransaksiPembelianBarang $record) {
                        $record->update([
                            'STATUS_TRANSAKSI' => 'Selesai',
                            'STATUS_BARANG' => 'Terjual',
                            'TGL_AMBIL_KIRIM' => Carbon::now(),
                        ]);

                        Notification::make()
                            ->title('Pengambilan barang telah dikonfirmasi')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (TransaksiPembelianBarang $record) {
                        $record->delete();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Hapus')
                    ->label('Hapus')
                    ->modalHeading('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaksiPembelianBarangs::route('/'),
            'create' => Pages\CreateTransaksiPembelianBarang::route('/create'),
            'edit' => Pages\EditTransaksiPembelianBarang::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        if ($user instanceof \App\Models\Pegawai) {
            return in_array(strtolower($user->jabatan), ['admin', 'owner', 'cs', 'kurir']);
        }

        return true;
    }
}
