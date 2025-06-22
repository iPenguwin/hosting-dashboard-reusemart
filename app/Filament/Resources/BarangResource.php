<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangResource\Pages;
use App\Filament\Resources\BarangResource\RelationManagers;
use App\Models\Barang;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Kelola Barang Titipan';

    public static ?string $label = 'Barang Titipan';

    protected static ?string $navigationGroup = 'Barang';

    public static function form(Form $form): Form
    {
        $isPenitip = auth()->guard('penitip')->check();

        return $form
            ->schema([
                DatePicker::make('TGL_MASUK')
                    ->dehydrated()
                    ->displayFormat('l, j F Y')
                    ->required(fn($operation) => $operation === 'create')
                    ->maxDate(now())
                    ->label('Tanggal Masuk')
                    ->default(now())
                    ->native(false)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $tglMasuk = Carbon::parse($state);
                            $set('TGL_KELUAR', $tglMasuk->copy()->addDays(30)->format('Y-m-d'));
                        } else {
                            $set('TGL_KELUAR', null);
                        }
                    })
                    ->columnSpanFull(),

                Radio::make('is_diantarkan_hunter')
                    ->label('Apakah barang diantar oleh Hunter?')
                    ->options([
                        1 => 'Ya, oleh Hunter',
                        0 => 'Tidak, oleh Penitip',
                    ])
                    ->default(0)
                    ->inline()
                    ->columnSpan(1)
                    ->reactive()
                    ->afterStateHydrated(function ($set, $get, $operation) {
                        if ($operation === 'edit') {
                            $value = !empty($get('ID_PEGAWAI')) ? 1 : 0;
                            $set('is_diantarkan_hunter', $value);
                        }
                    })
                    ->disabled(fn($operation) => $operation === 'edit')
                    ->columnSpanFull(),

                Section::make('Informasi Antar')
                    ->schema([
                        Select::make('ID_PENITIP')
                            ->required(fn($operation) => $operation === 'create')
                            ->disabled(fn($operation) => $operation === 'edit')
                            ->label('Penitip')
                            ->placeholder('Pilih Penitip')
                            ->relationship('penitip', 'NAMA_PENITIP')
                            ->searchable()
                            ->preload()
                            ->helperText('Penitip yang menitipkan barang ini')
                            ->columnSpan(function ($get, $operation) {
                                if ($operation === 'edit') {
                                    return $get('ID_PEGAWAI') ? 1 : 2;
                                }
                                return $get('is_diantarkan_hunter') == 0 ? 2 : 1;
                            }),

                        Select::make('ID_PEGAWAI')
                            ->disabled(fn($operation) => $operation === 'edit')
                            ->label('Hunter')
                            ->relationship('pegawai', 'NAMA_PEGAWAI', function ($query) {
                                return $query->whereHas('jabatans', function ($q) {
                                    $q->where('NAMA_JABATAN', 'Hunter');
                                });
                            })
                            ->searchable()
                            ->preload()
                            ->helperText('Hunter yang mengantarkan barang ke gudang')
                            ->visible(function ($get, $operation) {
                                if ($operation === 'edit') {
                                    return !is_null($get('ID_PEGAWAI'));
                                }
                                return $get('is_diantarkan_hunter') == 1;
                            })
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Section::make('Detail Barang')
                    ->schema([
                        TextInput::make('NAMA_BARANG')
                            ->required()
                            ->label('Nama Barang')
                            ->placeholder('Masukkan Nama Barang')
                            ->disabled($isPenitip)
                            ->maxLength(255),

                        Select::make('ID_KATEGORI')
                            ->required()
                            ->label('Kategori Barang')
                            ->disabled($isPenitip)
                            ->placeholder('Pilih Kategori')
                            ->relationship('kategoribarang', 'NAMA_KATEGORI')
                            ->searchable()
                            ->preload(),

                        MarkdownEditor::make('DESKRIPSI')
                            ->required()
                            ->disabled($isPenitip)
                            ->label('Deskripsi')
                            ->maxLength(1000)
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'italic',
                                'orderedList',
                                'preview',
                                'strike',
                            ]),

                        TextInput::make('HARGA_BARANG')
                            ->required()
                            ->prefix('Rp')
                            ->suffix(',00')
                            ->label('Harga Barang')
                            ->placeholder('Masukkan Harga Barang')
                            ->mask('99999999999999999999999999999')
                            ->columnSpanFull()
                            ->minValue(0)
                            ->helperText(function ($state) {
                                $numericValue = (int)preg_replace('/[^0-9]/', '', $state);
                                $formatted = number_format($numericValue, 0, ',', '.');
                                return "Harga: Rp {$formatted},00";
                            })
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                $cleanedValue = preg_replace('/[^0-9]/', '', $state);
                                $set('HARGA_BARANG', $cleanedValue);
                            }),

                        DatePicker::make('GARANSI')
                            ->label('Garansi')
                            ->disabled($isPenitip)
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->placeholder('Masukkan Tanggal Garansi')
                            ->nullable(),

                        TextInput::make('BERAT')
                            ->disabled($isPenitip)
                            ->label('Berat')
                            ->numeric()
                            ->extraInputAttributes([
                                'onkeypress' => 'return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 44 || event.charCode == 46',
                                'oninput' => 'this.value = this.value.replace(/[^0-9,.]/g, "").replace(/,/g, ".")'
                            ])
                            ->minValue(0)
                            ->step(0.01)
                            ->placeholder('Masukkan berat')
                            ->suffix('kg')
                            ->rules([
                                'numeric',
                                'regex:/^\d+(\.\d{1,2})?$/',
                            ]),

                        TextInput::make('RATING')
                            ->required()
                            ->disabled($isPenitip)
                            ->disabled()
                            ->label('Rating')
                            ->placeholder('Masukkan Rating')
                            ->numeric()
                            ->mask('999')
                            ->minValue(0)
                            ->maxValue(5)
                            ->default(0),

                        Select::make('STATUS_BARANG')
                            ->required()
                            ->disabled($isPenitip)
                            ->label('Status Barang')
                            ->placeholder('Masukkan Status Barang')
                            ->default('Tersedia')
                            ->options([
                                'Tersedia' => 'Tersedia',
                                'Dipesan' => 'Dipesan',
                                'Terjual' => 'Terjual',
                                'Tidak Terjual' => 'Tidak Terjual',
                                'Menunggu Diambil' => 'Menunggu Diambil',
                                'Diambil' => 'Diambil',
                                'Menunggu Dikirim' => 'Menunggu Dikirim',
                                'Dalam Pengiriman' => 'Dalam Pengiriman',
                                'Terkirim' => 'Terkirim',
                                'Untuk Donasi' => 'Untuk Donasi',
                                'Didonasikan' => 'Didonasikan',
                                'Diperpanjang' => 'Diperpanjang',
                                'Tidak Tersedia' => 'Tidak Tersedia',
                            ]),
                    ])
                    ->columns(2)
                    ->columnSpan(1),
                Section::make('Gambar Barang')
                    ->schema([
                        FileUpload::make('FOTO_BARANG')
                            ->label('Foto Barang')
                            ->columnSpanFull()
                            ->directory('barang')
                            ->required()
                            ->disabled($isPenitip)
                            ->image()
                            ->multiple()
                            ->maxSize(2048)
                            ->reorderable()
                            ->appendFiles()
                            ->helperText($isPenitip ? 'Gambar barang' : 'Unggah satu atau lebih gambar. Anda dapat mengubah urutannya.')
                            ->imageEditor()
                            ->downloadable(),
                    ])
                    ->columns(1)
                    ->columnSpan(1),

                DatePicker::make('TGL_KELUAR')
                    ->label('Tanggal Keluar (Otomatis)')
                    ->dehydrated()
                    ->native(false)
                    ->nullable(),

                DatePicker::make('TGL_AMBIL')
                    ->label('Tanggal Ambil')
                    ->hidden()
                    ->disabled(fn() => !auth()->guard('penitip')->check())
                    ->placeholder('Masukkan Tanggal Ambil')
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('KODE_BARANG')
                    ->copyable()
                    ->searchable()
                    ->label('Kode Barang')
                    ->sortable(),

                TextColumn::make('NAMA_BARANG')
                    ->searchable()
                    ->copyable()
                    ->label('Nama Barang')
                    ->sortable(),

                TextColumn::make('kategoribarang.NAMA_KATEGORI')
                    ->label('Nama Kategori')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('penitip.NAMA_PENITIP')
                    ->label('Nama Penitip')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('pegawai.NAMA_PEGAWAI')
                    ->label('Hunter')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Tidak ada Hunter')
                    ->getStateUsing(function ($record) {
                        return $record->pegawai ? $record->pegawai->NAMA_PEGAWAI : null;
                    }),

                ImageColumn::make('FOTO_BARANG')
                    ->label('Foto')
                    ->disk('public')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->height(60)
                    ->width(60)
                    ->getStateUsing(function ($record): ?array {
                        $fotoBarang = $record->FOTO_BARANG;
                        if (is_array($fotoBarang)) {
                            $files = $fotoBarang;
                        } elseif (is_string($fotoBarang)) {
                            $decodedFiles = json_decode($fotoBarang, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedFiles)) {
                                $files = $decodedFiles;
                            } else {
                                $files = [$fotoBarang];
                            }
                        } else {
                            return [];
                        }

                        if (empty($files)) {
                            return [];
                        }

                        return collect($files)
                            ->map(function ($file) {
                                if (empty($file) || !is_string($file)) {
                                    return null;
                                }
                                if (strpos($file, 'barang/') === 0) {
                                    return $file;
                                }
                                return 'bukti_transfer/' . $file;
                            })
                            ->filter()
                            ->toArray();
                    }),

                TextColumn::make('HARGA_BARANG')
                    ->money('IDR')
                    ->copyable()
                    ->searchable()
                    ->label('Harga Barang')
                    ->sortable(),

                TextColumn::make('STATUS_BARANG')
                    ->label('Status')
                    ->formatStateUsing(function ($state, Barang $record) {
                        $masaNormal = Carbon::parse($record->TGL_MASUK)->addDays(30);
                        $isDiperpanjang = $record->TGL_KELUAR && $record->TGL_KELUAR->gt($masaNormal);

                        return $state . ($isDiperpanjang ? ' (Diperpanjang)' : '');
                    })
                    ->badge()
                    ->color(function ($state, Barang $record) {
                        $masaNormal = Carbon::parse($record->TGL_MASUK)->addDays(30);
                        $isDiperpanjang = $record->TGL_KELUAR && $record->TGL_KELUAR->gt($masaNormal);

                        if ($isDiperpanjang) {
                            return 'primary';
                        }

                        return match ($state) {
                            'Tersedia' => 'success',
                            'Dipesan' => 'warning',
                            'Terjual' => 'danger',
                            'Didonasikan' => 'primary',
                            'Untuk Donasi' => 'info',
                            default => 'gray',
                        };
                    }),

                TextColumn::make('TGL_MASUK')
                    ->date('d-m-Y')
                    ->label('Tgl Masuk Barang')
                    ->sortable(),

                TextColumn::make('TGL_KELUAR')
                    ->date('d-m-Y')
                    ->label('Tgl Berakhir Penitipan')
                    ->sortable()
                    ->placeholder('N/A'),

                TextColumn::make('TGL_AMBIL')
                    ->date('d-m-Y')
                    ->label('Tgl Ambil')
                    ->sortable()
                    ->placeholder('N/A'),

                TextColumn::make('GARANSI')
                    ->date('d-m-Y')
                    ->label('Garansi')
                    ->sortable()
                    ->placeholder('N/A'),

                TextColumn::make('BERAT')
                    ->label('Berat')
                    ->formatStateUsing(fn($state) => $state ? $state . ' kg' : '-')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('DESKRIPSI')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->tooltip(fn($record) => $record->DESKRIPSI)
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('RATING')
                    ->label('Rating')
                    ->formatStateUsing(fn($state) => $state . ' / 5')
                    ->sortable()
                    ->searchable(),

                ViewColumn::make('RATING')
                    ->label('Rating')
                    ->view('components.filament.tables.columns.rating'),
            ])
            ->filters([
                Tables\Filters\Filter::make('perlu_didonasikan')
                    ->label('Barang Perlu Didonasikan')
                    ->query(function (Builder $query) {
                        $now = Carbon::now();
                        return $query->where(function ($q) use ($now) {
                            $q->whereIn('STATUS_BARANG', ['Tersedia', 'Diperpanjang'])
                                ->whereDate('TGL_KELUAR', '<=', $now->subDays(2));
                        });
                    }),
                Tables\Filters\Filter::make('perlu_perpanjang')
                    ->label('Barang Perlu Diperpanjang')
                    ->query(fn(Builder $query): Builder => $query->whereDate('TGL_KELUAR', '<=', Carbon::tomorrow())),

                Tables\Filters\Filter::make('barang_tidak_laku')
                    ->label('Barang Tidak Laku')
                    ->query(fn(Builder $query): Builder => $query->where('STATUS_BARANG', 'Tidak Terjual')
                        ->orWhere(function ($q) {
                            $q->where('STATUS_BARANG', 'Diperpanjang')
                                ->whereDate('TGL_KELUAR', '<=', Carbon::today());
                        })),

                Tables\Filters\Filter::make('barang_didonasikan')
                    ->label('Barang Didonasikan')
                    ->query(fn(Builder $query): Builder => $query->where('STATUS_BARANG', 'Untuk Donasi')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(function (): string {
                        if (auth()->guard('penitip')->check()) {
                            return 'Detail';
                        }
                        return 'Edit';
                    })
                    ->icon(function (): string {
                        if (auth()->guard('penitip')->check()) {
                            return 'heroicon-o-eye';
                        }
                        return 'heroicon-o-pencil-square';
                    }),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Barang $record) {
                        $record->delete();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Barang')
                    ->label('Hapus')
                    ->modalHeading('Hapus'),

                Tables\Actions\Action::make('cetakNotaBarang')
                    ->label('Cetak Nota')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->action(function (Barang $record) {
                        $pdf = Pdf::loadView('pdf.nota_barang', [
                            'barang' => $record->load([
                                'penitip',
                                'detailTransaksiPenitipans.transaksiPenitipan.pegawaiTransaksiPenitipans.pegawai',
                                'pegawai'
                            ])
                        ]);

                        return response()->streamDownload(
                            fn() => print($pdf->output()),
                            "Nota_Barang_{$record->KODE_BARANG}.pdf"
                        );
                    }),

                Tables\Actions\Action::make('donasikan_otomatis')
                    ->label('Donasikan Barang')
                    ->icon('heroicon-o-gift')
                    ->color('danger')
                    ->visible(function (Barang $record) {
                        $isPegawai = auth()->guard('pegawai')->check();
                        if (!$isPegawai) return false;

                        $user = auth()->guard('pegawai')->user();
                        $isOwnerOrAdmin = in_array($user->jabatan, ['Owner', 'Admin', 'cs']);

                        if (!$isOwnerOrAdmin) return false;

                        $now = Carbon::now();
                        $tglKeluar = Carbon::parse($record->TGL_KELUAR);

                        $isExpired = $now->diffInDays($tglKeluar, false) <= -2;
                        $isAvailable = $record->STATUS_BARANG === 'Tersedia';

                        $totalDays = Carbon::parse($record->TGL_MASUK)->diffInDays($tglKeluar);
                        $isFirstPeriod = $totalDays >= 30;

                        return $isExpired && $isAvailable && $isFirstPeriod;
                    })
                    ->action(function (Barang $record) {
                        $record->update([
                            'STATUS_BARANG' => 'Untuk Donasi',
                            'TGL_AMBIL' => now()
                        ]);

                        Notification::make()
                            ->title('Barang berhasil didonasikan secara otomatis')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Donasikan Barang')
                    ->modalDescription(function (Barang $record) {
                        $tglKeluar = Carbon::parse($record->TGL_KELUAR)->format('d/m/Y');
                        $totalDays = Carbon::parse($record->TGL_MASUK)->diffInDays(Carbon::parse($record->TGL_KELUAR));

                        $period = $totalDays >= 60 ? '60 hari (setelah perpanjangan)' : '30 hari (masa penitipan awal)';

                        return "Masa penitipan {$period} telah berakhir pada {$tglKeluar} dan barang belum diambil selama 2 hari. Apakah Anda yakin ingin mendonasikan barang ini?";
                    })
                    ->modalSubmitActionLabel('Ya, Donasikan'),

                Tables\Actions\Action::make('ambil_barang')
                    ->label('Ambil Barang')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->visible(function (Barang $record) {
                        $isPenitip = auth()->guard('penitip')->check();
                        $isPegawai = auth()->guard('pegawai')->check();
                        $penitipId = $isPenitip ? auth()->guard('penitip')->user()->ID_PENITIP : null;

                        $daysSinceEntry = Carbon::parse($record->TGL_MASUK)->diffInDays(Carbon::now(), false);
                        $daysUntilExpire = Carbon::now()->diffInDays(Carbon::parse($record->TGL_KELUAR), false);

                        return ($isPenitip && $record->ID_PENITIP == $penitipId || $isPegawai) &&
                            $record->STATUS_BARANG === 'Tersedia' &&
                            $daysSinceEntry >= 27 &&
                            $daysSinceEntry <= 30 &&
                            $daysUntilExpire >= 0 &&
                            $daysUntilExpire <= 3;
                    })
                    ->form([
                        DatePicker::make('TGL_AMBIL')
                            ->label('Tanggal Pengambilan')
                            ->required()
                            ->default(now())
                            ->maxDate(now())
                            ->native(false)
                            ->helperText(function ($record) {
                                return 'Maksimal 2 hari setelah tanggal kadaluarsa (' . Carbon::parse($record->TGL_KELUAR)->format('d/m/Y') . ')';
                            })
                    ])
                    ->action(function (Barang $record, array $data) {
                        $record->update([
                            'TGL_AMBIL' => $data['TGL_AMBIL'],
                            'STATUS_BARANG' => 'Tidak Tersedia'
                        ]);

                        Notification::make()
                            ->title('Pengambilan barang berhasil dicatat')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pengambilan Barang')
                    ->modalDescription(function (Barang $record) {
                        $expireDate = Carbon::parse($record->TGL_KELUAR)->format('d/m/Y');
                        $userType = auth()->guard('penitip')->check() ? 'Penitip' : 'Pegawai';
                        return "Masa penitipan berakhir pada {$expireDate}. Apakah Anda yakin ingin mencatat pengambilan barang ini? (Aksi dilakukan sebagai {$userType})";
                    })
                    ->modalSubmitActionLabel('Ya, Simpan'),

                Tables\Actions\Action::make('perpanjang')
                    ->label('Perpanjang')
                    ->icon('heroicon-o-clock')
                    ->visible(function (Barang $record) {
                        $daysSinceEntry = Carbon::parse($record->TGL_MASUK)->diffInDays(Carbon::now(), false);
                        $totalDays = Carbon::parse($record->TGL_MASUK)->diffInDays(Carbon::parse($record->TGL_KELUAR), false);

                        return $daysSinceEntry >= 27 &&
                            $daysSinceEntry <= 30 &&
                            $totalDays < 30 &&
                            ($record->STATUS_BARANG === 'Tersedia' || $record->STATUS_BARANG === 'Diperpanjang');
                    })
                    ->action(function (Barang $record) {
                        $newTglKeluar = Carbon::parse($record->TGL_KELUAR)->addDays(30);

                        if ($record->STATUS_BARANG === 'Tersedia') {
                            $record->update([
                                'TGL_KELUAR' => $newTglKeluar,
                                'STATUS_BARANG' => 'Tersedia'
                            ]);

                            Notification::make()
                                ->title('Perpanjangan 30 hari berhasil')
                                ->sendToDatabase($record->penitip);
                        } elseif (
                            $record->STATUS_BARANG === 'Tersedia' &&
                            Carbon::parse($record->TGL_MASUK)->diffInDays($newTglKeluar) < 60
                        ) {
                            $record->update(['TGL_KELUAR' => $newTglKeluar]);
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Perpanjang Masa Titip')
                    ->modalDescription('Apakah Anda yakin ingin memperpanjang masa penitipan barang ini selama 30 hari?')
                    ->modalSubmitActionLabel('Ya, Perpanjang'),

                Tables\Actions\Action::make('donasikan')
                    ->label('Donasikan')
                    ->icon('heroicon-o-gift')
                    ->color('info')
                    ->visible(function (Barang $record) {
                        $isPegawai = auth()->guard('pegawai')->check();
                        if (!$isPegawai) return false;

                        $user = auth()->guard('pegawai')->user();
                        $isOwnerOrAdmin = in_array($user->jabatan, ['Owner', 'Admin', 'cs']);

                        $isUnclaimed = $record->STATUS_BARANG === 'Perlu Diambil' &&
                            $record->detailTransaksiPenitipans()->whereHas('transaksiPenitipan', function ($q) {
                                $q->where('STATUS_PEMBAYARAN', 'Lunas')
                                    ->where('STATUS_TRANSAKSI', 'Perlu Diambil')
                                    ->where('created_at', '<=', Carbon::now()->subDays(2));
                            })->exists();

                        $isExtended = $record->STATUS_BARANG === 'Diperpanjang' &&
                            Carbon::parse($record->TGL_KELUAR)->diffInDays(Carbon::parse($record->TGL_MASUK)) >= 60;

                        return $isOwnerOrAdmin && ($isUnclaimed || $isExtended);
                    })
                    ->action(function (Barang $record) {
                        $record->update([
                            'STATUS_BARANG' => 'Untuk Donasi',
                            'TGL_AMBIL' => now()
                        ]);

                        Notification::make()
                            ->title('Barang berhasil didonasikan secara otomatis')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Donasikan Barang')
                    ->modalDescription(function (Barang $record) {
                        $tglKeluar = Carbon::parse($record->TGL_KELUAR)->format('d/m/Y');
                        $totalDays = Carbon::parse($record->TGL_MASUK)->diffInDays(Carbon::parse($record->TGL_KELUAR));

                        $period = $totalDays >= 60 ? '60 hari (setelah perpanjangan)' : '30 hari (masa penitipan awal)';

                        return "Masa penitipan {$period} telah berakhir pada {$tglKeluar} dan barang belum diambil selama 2 hari. Apakah Anda yakin ingin mendonasikan barang ini?";
                    })
                    ->modalSubmitActionLabel('Ya, Donasikan'),

                Tables\Actions\Action::make('konfirmasi_pengambilan')
                    ->label('Konfirmasi Pengambilan')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(function (Barang $record) {
                        $isPenitip = auth()->guard('penitip')->check();
                        $penitipId = $isPenitip ? auth()->guard('penitip')->user()->ID_PENITIP : null;

                        return $isPenitip &&
                            $record->ID_PENITIP == $penitipId &&
                            $record->STATUS_BARANG === 'Tidak Terjual' &&
                            is_null($record->TGL_AMBIL);
                    })
                    ->form([
                        DatePicker::make('TGL_AMBIL')
                            ->label('Tanggal Ambil')
                            ->required()
                            ->native(false)
                            ->default(now()),
                    ])
                    ->action(function (Barang $record, array $data) {
                        $record->update([
                            'TGL_AMBIL' => $data['TGL_AMBIL'],
                            'STATUS_BARANG' => 'Tidak Tersedia'
                        ]);

                        Notification::make()
                            ->title('Pengambilan barang berhasil dikonfirmasi')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pengambilan Barang')
                    ->modalDescription('Silakan masukkan tanggal pengambilan barang dan konfirmasi')
                    ->modalSubmitActionLabel('Ya, Konfirmasi'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            $records->each(function (Barang $record) {
                                $record->delete();
                            });
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBarangs::route('/'),
            'create' => Pages\CreateBarang::route('/create'),
            'edit' => Pages\EditBarang::route('/{record}/edit'),
            'laporan-titipan-masa-habis' => Pages\LaporanTitipanMasaHabis::route('/laporan-titipan-masa-habis'),
        ];
    }

    public static function getNavigationItems(): array
    {
        $items = parent::getNavigationItems();

        $items[] = NavigationItem::make('Laporan Titipan Habis')
            ->url(static::getUrl('laporan-titipan-masa-habis'))
            ->icon('heroicon-o-document-text')
            ->sort(3);

        return $items;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->guard('penitip')->check()) {
            $penitipId = auth()->guard('penitip')->user()->ID_PENITIP;
            return $query->where('ID_PENITIP', $penitipId);
        }

        return $query;
    }
}
