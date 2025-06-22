<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RequestResource\Pages;
use App\Filament\Resources\RequestResource\RelationManagers;
use App\Models\Barang;
use App\Models\Organisasi;
use App\Models\Request;
use App\Models\TransaksiDonasi;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RequestResource extends Resource
{
    protected static ?string $model = Request::class;

    protected static ?string $navigationLabel = 'Transaksi Request Barang';

    public static ?string $label = 'Transaksi Request Barang';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Organisasi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('ID_ORGANISASI')
                    ->default(function () {
                        return Auth::user()->ID_ORGANISASI ?? null;
                    }),

                TextInput::make('NAMA_BARANG_REQUEST')
                    ->required()
                    ->label('Barang yang dibutuhkan')
                    ->unique(ignoreRecord: true)
                    ->rules([
                        function () {
                            return function (string $attribute, $value, Closure $fail) {
                                $existingRequest = Request::where('NAMA_BARANG_REQUEST', $value)
                                    ->where('ID_ORGANISASI', Auth::user()->ID_ORGANISASI)
                                    ->where('STATUS_REQUEST', 'Menunggu')
                                    ->exists();

                                if ($existingRequest) {
                                    $fail("Barang '$value' sudah ada dalam antrian request organisasi Anda. Silakan pilih nama barang lainnya atau tunggu hingga request sebelumnya diproses.");
                                }
                            };
                        },
                    ]),

                Select::make('ID_BARANG')
                    ->label('Pilih Barang untuk Donasi')
                    ->options(Barang::where('STATUS_BARANG', 'Untuk Didonasikan')->pluck('NAMA_BARANG', 'ID_BARANG'))
                    ->searchable()
                    ->preload()
                    ->visible(fn() => Auth::user()->JABATAN === 'Owner')
                    // ->required(fn() => Auth::user()->JABATAN === 'Owner')
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $barang = Barang::find($state);
                            $set('NAMA_BARANG_REQUEST', $barang->NAMA_BARANG);
                        }
                    }),

                Textarea::make('DESKRIPSI_REQUEST')
                    ->label('Deskripsi Request')
                    ->required()
                    ->maxLength(255),

                Select::make('STATUS_REQUEST')
                    ->label('Status Request')
                    ->options([
                        'Menunggu' => 'Menunggu',
                        'Diterima' => 'Diterima',
                        'Ditolak' => 'Ditolak',
                    ])
                    ->default('Menunggu')
                    ->dehydrated()
                    ->visible(fn() => Auth::user()->JABATAN === 'Owner')
                    ->required(fn() => Auth::user()->JABATAN === 'Owner'),

                Hidden::make('STATUS_REQUEST')
                    ->default('Menunggu')
                    ->visible(fn() => Auth::user()->JABATAN !== 'Owner')
                    ->dehydrated(),

                // Select::make('ID_ORGANISASI')
                //     ->label('ID Organisasi')
                //     ->options(Organisasi::all()->pluck('NAMA_ORGANISASI', 'ID_ORGANISASI'))
                //     ->searchable()
                //     ->preload(),

                // Hidden::make('ID_ORGANISASI')
                //     ->default(function () {
                //         return Auth::user()->ID_ORGANISASI; // sesuaikan dengan struktur user kamu
                //     }),
                // Hidden::make('ID_ORGANISASI')
                //     ->default(function () {
                //         return Auth::user()->ID_ORGANISASI ?? null;
                //     }),

                // TextInput::make('NAMA_BARANG_REQUEST')
                //     ->required()
                //     ->label('Masukkan Barang yang dibutuhkan'),

                // Select::make('ID_BARANG')
                //     ->label('Barang')
                //     ->options(Barang::all()->pluck('NAMA_BARANG', 'ID_BARANG'))
                //     ->searchable()
                //     ->preload(),

                // Select::make('ID_BARANG')
                //     ->label('Barang')
                //     ->options(function () {
                //         $user = Auth::user();
                //         $requestedBarangIds = Request::where('ID_ORGANISASI', $user->ID_ORGANISASI)
                //             ->pluck('ID_BARANG')
                //             ->toArray();

                //         return Barang::whereNotIn('ID_BARANG', $requestedBarangIds)
                //             ->where('STATUS_BARANG', 'Tidak Terjual') // Hanya tampilkan yang statusnya Tidak Terjual
                //             ->pluck('NAMA_BARANG', 'ID_BARANG');
                //     })
                //     ->searchable()
                //     ->preload()
                //     ->required()
                //     ->disabled(fn() => Auth::user()?->JABATAN === 'Pegawai'),

                // DatePicker::make('CREATE_AT')
                //     ->label('Tanggal Request')
                //     ->required()
                //     ->default(now()),

                // Textarea::make('DESKRIPSI_REQUEST')
                //     ->label('Deskripsi Request')
                //     ->required()
                //     ->disabled(fn() => Auth::user()?->JABATAN === 'Pegawai')
                //     ->maxLength(255),

                // Select::make('STATUS_REQUEST')
                //     ->label('Status Request')
                //     ->options([
                //         'Menunggu' => 'Menunggu',
                //         'Diterima' => 'Diterima',
                //         'Ditolak' => 'Ditolak',
                //     ])
                //     ->default('Menunggu')
                //     ->required(),

                // Select::make('STATUS_REQUEST')
                //     ->label('Status Request')
                //     ->options([
                //         'Menunggu' => 'Menunggu',
                //         'Diterima' => 'Diterima',
                //         'Ditolak' => 'Ditolak',
                //     ])
                //     ->default('Menunggu')
                //     ->required()
                //     ->hidden(fn() => Auth::user()->JABATAN !== 'Owner'),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = Auth::user();

        return $table
            ->columns([
                // TextColumn::make('ID_REQUEST')
                //     ->label('ID Request')
                //     ->sortable()
                //     ->searchable(),
                // $user instanceof \App\Models\Pegawai ? TextColumn::make('ID_REQUEST')
                //     ->label('ID Request')
                //     ->sortable()
                //     ->searchable()
                //     : null,
                TextColumn::make('ID_REQUEST')
                    ->label('ID Request')
                    ->sortable()
                    ->searchable()
                    ->visible(fn() => Auth::user() instanceof \App\Models\Pegawai),

                TextColumn::make('organisasi.NAMA_ORGANISASI')
                    ->label('Nama Organisasi')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('NAMA_BARANG_REQUEST')
                    ->label('Nama Barang')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('CREATE_AT')
                    ->label('Tanggal Request')
                    ->sortable()
                    ->searchable()
                    ->dateTime('d/m/Y'),

                TextColumn::make('DESKRIPSI_REQUEST')
                    ->label('Deskripsi Request')
                    ->sortable()
                    ->searchable()
                    ->limit(50),

                TextColumn::make('STATUS_REQUEST')
                    ->label('Status Request')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state): ?string => match ($state) {
                        'Menunggu' => 'warning',
                        'Diterima' => 'success',
                        'Ditolak' => 'danger',
                        default => null,
                    })
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('accept')
                    ->label('Terima Request')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(
                        fn(Request $record): bool =>
                        Auth::user()->JABATAN === 'Owner' &&
                            $record->STATUS_REQUEST === 'Menunggu'
                    )
                    ->form([
                        Forms\Components\Section::make('Detail Request')
                            ->schema([
                                Forms\Components\TextInput::make('NAMA_ORGANISASI')
                                    ->label('Organisasi')
                                    ->disabled()
                                    ->default(fn(Request $record) => $record->organisasi->NAMA_ORGANISASI),

                                Forms\Components\TextInput::make('NAMA_BARANG_REQUEST')
                                    ->label('Barang yang Diminta')
                                    ->disabled()
                                    ->default(fn(Request $record) => $record->NAMA_BARANG_REQUEST),

                                Forms\Components\Textarea::make('DESKRIPSI_REQUEST')
                                    ->label('Deskripsi Request')
                                    ->disabled()
                                    ->default(fn(Request $record) => $record->DESKRIPSI_REQUEST),
                            ])
                            ->columns(1),

                        Forms\Components\Section::make('Konfirmasi')
                            ->schema([
                                Select::make('ID_BARANG')
                                    ->label('Pilih Barang untuk Donasi')
                                    ->options(Barang::where('STATUS_BARANG', 'Untuk Donasi')->pluck('NAMA_BARANG', 'ID_BARANG'))
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('STATUS_REQUEST')
                                    ->label('Status Request')
                                    ->options([
                                        'Diterima' => 'Diterima',
                                        'Ditolak' => 'Ditolak',
                                    ])
                                    ->default('Diterima')
                                    ->required(),
                            ])
                            ->columns(2),
                    ])
                    ->action(function (Request $record, array $data) {
                        // Update status request
                        $record->update(['STATUS_REQUEST' => $data['STATUS_REQUEST']]);

                        $record->update(['STATUS_BARANG' => 'Didonasikan',]);

                        // Jika status diterima, alokasikan barang dan buat transaksi
                        if ($data['STATUS_REQUEST'] === 'Diterima') {
                            // Panggil method allocateBarang dari model Request
                            $record->allocateBarang($data['ID_BARANG']);

                            // Buat transaksi donasi
                            TransaksiDonasi::create([
                                'ID_ORGANISASI' => $record->ID_ORGANISASI,
                                'ID_REQUEST' => $record->ID_REQUEST,
                                'TGL_DONASI' => null,
                                'PENERIMA' => null
                            ]);
                        }
                    })
                    ->after(function () {
                        // Notifikasi sukses
                        Notification::make()
                            ->success()
                            ->title('Request berhasil diproses')
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Request $record) {
                        $record->delete();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Request Donasi Barang')
                    ->label('Hapus')
                    ->modalHeading('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        // Jika user adalah organisasi, tampilkan hanya request milik mereka
        if ($user instanceof \App\Models\Organisasi) {
            return parent::getEloquentQuery()
                ->where('ID_ORGANISASI', $user->ID_ORGANISASI);
        }

        // Default (pegawai/admin bisa lihat semua)
        return parent::getEloquentQuery();
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
            'index' => Pages\ListRequests::route('/'),
            'create' => Pages\CreateRequest::route('/create'),
            'edit' => Pages\EditRequest::route('/{record}/edit'),
        ];
    }
}
