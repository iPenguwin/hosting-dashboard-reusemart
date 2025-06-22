<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlamatResource\Pages;
use App\Models\Alamat;
use App\Models\Provinsi;
use App\Models\Kecamatan;
use App\Models\DesaKelurahan;
use App\Models\Kabupaten;
use App\Models\Pembeli;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AlamatResource extends Resource
{
    protected static ?string $model = Alamat::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationLabel = 'Alamat';

    public static ?string $label = 'Alamat';

    protected static ?string $navigationGroup = 'User';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('ID_PEMBELI')
                    ->label('Pembeli')
                    ->options(Pembeli::all()->pluck('NAMA_PEMBELI', 'ID_PEMBELI'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->columnSpanFull(),

                TextInput::make('JUDUL')
                    ->required()
                    ->label('Judul Alamat')
                    ->placeholder('Contoh: Rumah, Kantor, dll')
                    ->maxLength(255),

                TextInput::make('NAMA_JALAN')
                    ->required()
                    ->label('Nama Jalan/Alamat')
                    ->placeholder('Contoh: Jl. Mawar No.123')
                    ->maxLength(255),

                Select::make('PROVINSI')
                    ->label('Provinsi')
                    ->placeholder('Pilih Provinsi')
                    ->options(Provinsi::all()->pluck('nama_provinsi', 'id_provinsi'))
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $set('KABUPATEN', null);
                        $set('KECAMATAN', null);
                        $set('DESA_KELURAHAN', null);
                    })
                    ->searchable(),


                Select::make('KABUPATEN')
                    ->label('Kabupaten/Kota')
                    ->placeholder('Pilih Kabupaten/Kota')
                    ->options(function (callable $get) {
                        $provinsiId = $get('PROVINSI');
                        if (!$provinsiId) {
                            return [];
                        }
                        return Kabupaten::where('id_provinsi', $provinsiId)
                            ->pluck('nama_kabupaten_kota', 'id_kabupaten_kota');
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $set('KECAMATAN', null);
                        $set('DESA_KELURAHAN', null);
                    })
                    ->searchable()
                    ->disabled(fn(callable $get) => !$get('PROVINSI')),

                Select::make('KECAMATAN')
                    ->label('Kecamatan')
                    ->placeholder('Pilih Kecmatan')
                    ->options(function (callable $get) {
                        $kabupatenId = $get('KABUPATEN');
                        if (!$kabupatenId) {
                            return [];
                        }
                        return Kecamatan::where('id_kabupaten_kota', $kabupatenId)
                            ->pluck('nama_kecamatan', 'id_kecamatan');
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $set('DESA_KELURAHAN', null);
                    })
                    ->searchable()
                    ->disabled(fn(callable $get) => !$get('KABUPATEN')),

                Select::make('DESA_KELURAHAN')
                    ->label('Desa/Kelurahan')
                    ->placeholder('Pilih Desa/Kelurahan')
                    ->options(function (callable $get) {
                        $kecamatanId = $get('KECAMATAN');
                        if (!$kecamatanId) {
                            return [];
                        }
                        return DesaKelurahan::where('id_kecamatan', $kecamatanId)
                            ->pluck('nama_desa_kelurahan', 'id_desa_kelurahan');
                    })
                    ->required()
                    ->searchable()
                    ->disabled(fn(callable $get) => !$get('KECAMATAN')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pembeli.NAMA_PEMBELI')
                    ->label('Nama Pembeli')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('JUDUL')
                    ->label('Judul Alamat')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('NAMA_JALAN')
                    ->label('Nama Jalan')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('DESA_KELURAHAN')
                    ->label('Desa/Kelurahan')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('KECAMATAN')
                    ->label('Kecamatan')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('KABUPATEN')
                    ->label('Kabupaten')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('PROVINSI')
                    ->label('Provinsi')
                    ->sortable()
                    ->searchable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Alamat $record) {
                        $record->delete();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Alamat')
                    ->label('Hapus')
                    ->modalSubmitActionLabel('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListAlamats::route('/'),
            'create' => Pages\CreateAlamat::route('/create'),
            'edit' => Pages\EditAlamat::route('/{record}/edit'),
        ];
    }

    public static function fillEditForm(Alamat $record, Form $form): Form
    {
        // Cari ID wilayah berdasarkan nama yang tersimpan
        $provinsi = Provinsi::where('nama_provinsi', $record->PROVINSI)->first();
        $kabupaten = Kabupaten::where('nama_kabupaten_kota', $record->KABUPATEN)->first();
        $kecamatan = Kecamatan::where('nama_kecamatan', $record->KECAMATAN)->first();
        $desa = DesaKelurahan::where('nama_desa_kelurahan', $record->DESA_KELURAHAN)->first();

        return $form
            ->schema(self::form($form)->getComponents())
            ->state([
                'ID_PEMBELI' => $record->ID_PEMBELI,
                'JUDUL' => $record->JUDUL,
                'NAMA_ALAMAT' => $record->NAMA_ALAMAT,
                'PROVINSI' => $provinsi->id_provinsi ?? null,
                'KABUPATEN' => $kabupaten->id_kabupaten_kota ?? null,
                'KECAMATAN' => $kecamatan->id_kecamatan ?? null,
                'DESA_KELURAHAN' => $desa->id_desa_kelurahan ?? null,
            ]);
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        if ($user instanceof \App\Models\Pegawai) {
            return in_array(strtolower($user->jabatan), ['admin']);
        }

        return true;
    }

}
