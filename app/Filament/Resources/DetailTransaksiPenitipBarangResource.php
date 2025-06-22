<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DetailTransaksiPenitipBarangResource\Pages;
use App\Filament\Resources\DetailTransaksiPenitipBarangResource\RelationManagers;
use App\Models\Barang;
use App\Models\DetailTransaksiPenitipBarang;
use App\Models\TransaksiPenitipanBarang;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DetailTransaksiPenitipBarangResource extends Resource
{
    protected static ?string $model = DetailTransaksiPenitipBarang::class;

    protected static ?string $navigationLabel = 'Detail Transaksi Penitipan';

    public static ?string $label = 'Detail Transaksi Penitipan Barang';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('ID_TRANSAKSI_PENITIPAN')
                    ->label('ID Transaksi Penitipan')
                    ->options(TransaksiPenitipanBarang::all()->pluck('ID_TRANSAKSI_PENITIPAN', 'ID_TRANSAKSI_PENITIPAN'))
                    ->searchable()
                    ->preload(),
                Select::make('ID_BARANG')
                    ->label('ID Barang')
                    ->options(Barang::all()->pluck('NAMA_BARANG', 'ID_BARANG'))
                    ->searchable()
                    ->preload()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ID_DETAIL_TRANSAKSI_PENITIPAN')
                    ->label('ID Detail Transaksi Penitipan'),
                TextColumn::make('ID_TRANSAKSI_PENITIPAN')
                    ->label('ID Transaksi Penitipan'),
                TextColumn::make('ID_BARANG')
                    ->label('ID Barang')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (DetailTransaksiPenitipBarang $record) {
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDetailTransaksiPenitipBarangs::route('/'),
            'create' => Pages\CreateDetailTransaksiPenitipBarang::route('/create'),
            'edit' => Pages\EditDetailTransaksiPenitipBarang::route('/{record}/edit'),
        ];
    }
}
