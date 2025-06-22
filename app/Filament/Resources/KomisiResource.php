<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KomisiResource\Pages;
use App\Filament\Resources\KomisiResource\RelationManagers;
use App\Models\Komisi;
use App\Models\Pegawai;
use App\Models\Penitip;
use App\Models\TransaksiPembelianBarang;
use Dom\Text;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KomisiResource extends Resource
{
    protected static ?string $model = Komisi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Komisi';

    public static ?string $label = 'Komisi';

    protected static ?string $navigationGroup = 'User';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('JENIS_KOMISI')
                    ->required()
                    ->label('Jenis Komisi')
                    ->placeholder('Pilih Jenis Komisi')
                    ->options([
                        'Hunter' => 'Hunter',
                        'Penitip' => 'Penitip',
                        'Reusemart' => 'Reusemart',
                    ])
                    ->reactive(),

                Select::make('ID_PENITIP')
                    ->label('Penitip')
                    ->options(Penitip::pluck('NAMA_PENITIP', 'ID_PENITIP'))
                    ->required(fn(callable $get) => $get('JENIS_KOMISI') === 'Penitip')
                    ->searchable()
                    ->preload()
                    ->visible(fn(callable $get) => $get('JENIS_KOMISI') === 'Penitip'),

                Select::make('ID_PEGAWAI')
                    ->label('Pegawai Hunter')
                    ->options(
                        Pegawai::whereHas('jabatans', function ($query) {
                            $query->whereRaw('LOWER(NAMA_JABATAN) = ?', ['hunter']);
                        })->pluck('NAMA_PEGAWAI', 'ID_PEGAWAI')
                    )
                    ->required(fn(callable $get) => $get('JENIS_KOMISI') === 'Hunter')
                    ->searchable()
                    ->preload()
                    ->visible(fn(callable $get) => $get('JENIS_KOMISI') === 'Hunter'),

                Select::make('ID_TRANSAKSI_PEMBELIAN')
                    ->label('ID Transaksi Pembelian')
                    ->options(TransaksiPembelianBarang::all()->pluck('ID_TRANSAKSI_PEMBELIAN', 'ID_TRANSAKSI_PEMBELIAN'))
                    ->required()
                    ->searchable()
                    ->preload(),

                TextInput::make('NOMINAL_KOMISI')
                    ->required()
                    ->label('Nominal Komisi')
                    ->placeholder('Masukkan Nominal Komisi')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ID_KOMISI')
                    ->label('ID Komisi')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('JENIS_KOMISI')
                    ->label('Jenis Komisi')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('penitips.NAMA_PENITIP')
                    ->label('Nama Penitip')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('pegawais.NAMA_PEGAWAI')
                    ->label('Nama Pegawai')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('transaksi_pembelian_barangs.ID_TRANSAKSI_PEMBELIAN')
                    ->label('ID Transaksi Pembelian')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('NOMINAL_KOMISI')
                    ->label('Nominal Komisi')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Komisi $record) {
                        $record->delete();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Komisi')
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKomisis::route('/'),
            'create' => Pages\CreateKomisi::route('/create'),
            'edit' => Pages\EditKomisi::route('/{record}/edit'),
        ];
    }
}
