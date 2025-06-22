<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PegawaiTransaksiPembelianResource\Pages;
use App\Filament\Resources\PegawaiTransaksiPembelianResource\RelationManagers;
use App\Models\PegawaiTransaksiPembelian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PegawaiTransaksiPembelianResource extends Resource
{
    protected static ?string $model = PegawaiTransaksiPembelian::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('ID_PEGAWAI')
                    ->label('Pegawai')
                    ->options(\App\Models\Pegawai::all()->pluck('NAMA_PEGAWAI', 'ID_PEGAWAI'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->columnSpanFull(),
                Forms\Components\Select::make('ID_TRANSAKSI_PEMBELIAN')
                    ->label('Transaksi Pembelian')
                    ->options(\App\Models\TransaksiPembelianBarang::all()->pluck('ID_TRANSAKSI_PEMBELIAN', 'ID_TRANSAKSI_PEMBELIAN'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pegawais.NAMA_PEGAWAI')
                    ->label('Nama Pegawai')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('transaksi_pembelian_barangs.ID_TRANSAKSI_PEMBELIAN')
                    ->label('ID Transaksi Pembelian')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (PegawaiTransaksiPembelian $record) {
                        $record->delete();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Transaksi')
                    ->label('Apakah Anda yakin ingin menghapus transaksi ini?')
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
            'index' => Pages\ListPegawaiTransaksiPembelians::route('/'),
            'create' => Pages\CreatePegawaiTransaksiPembelian::route('/create'),
            'edit' => Pages\EditPegawaiTransaksiPembelian::route('/{record}/edit'),
        ];
    }
}
