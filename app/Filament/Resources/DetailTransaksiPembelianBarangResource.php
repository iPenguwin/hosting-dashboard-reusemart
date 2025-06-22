<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DetailTransaksiPembelianBarangResource\Pages;
use App\Filament\Resources\DetailTransaksiPembelianBarangResource\RelationManagers;
use App\Models\Barang;
use App\Models\DetailTransaksiPembelianBarang;
use App\Models\TransaksiPembelianBarang;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DetailTransaksiPembelianBarangResource extends Resource
{
    protected static ?string $model = DetailTransaksiPembelianBarang::class;

    protected static ?string $navigationLabel = 'Detail Transaksi Pembelian';

    public static ?string $label = 'Detail Transaksi Pembelian Barang';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('ID_TRANSAKSI_PENITIPAN')
                    ->label('ID Transaksi Pembelian')
                    ->options(TransaksiPembelianBarang::all()->pluck('ID_TRANSAKSI_PEMBELIAN', 'ID_TRANSAKSI_PEMBELIAN'))
                    ->searchable()
                    ->preload(),
                Select::make('ID_BARANG')
                    ->label('Barang')
                    ->options(Barang::all()->pluck('NAMA_BARANG', 'ID_BARANG'))
                    ->searchable()
                    ->preload()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ID_DETAIL_TRANSAKSI_PEMBELIAN')
                    ->label('ID Transaksi Pembelian'),
                TextColumn::make('ID_BARANG')
                    ->label('ID Barang')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListDetailTransaksiPembelianBarangs::route('/'),
            'create' => Pages\CreateDetailTransaksiPembelianBarang::route('/create'),
            'edit' => Pages\EditDetailTransaksiPembelianBarang::route('/{record}/edit'),
        ];
    }
}
