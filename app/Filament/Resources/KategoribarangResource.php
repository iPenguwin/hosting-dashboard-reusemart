<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoribarangResource\Pages;
use App\Filament\Resources\KategoribarangResource\RelationManagers;
use App\Models\Kategoribarang;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KategoribarangResource extends Resource
{
    protected static ?string $model = Kategoribarang::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Kategori Barang';

    public static ?string $label = 'Kategori Barang';

    protected static ?string $navigationGroup = 'Barang';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('NAMA_KATEGORI')
                    ->required()
                    ->label('Nama Kategori')
                    ->placeholder('Masukkan Nama Kategori')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ID_KATEGORI')
                    ->label('ID Kategori')
                    ->sortable()
                    ->placeholder('Masukkan ID Kategori')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('NAMA_KATEGORI')
                    ->label('Nama Kategori')
                    ->sortable()
                    ->placeholder('Masukkan Nama Kategori')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('JML_BARANG')
                    ->label('Total Barang Masuk')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('JML_TERJUAL')
                    ->label('Barang Terjual')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('JML_DIDONASIKAN')
                    ->label('Barang Didonasikan')
                    ->sortable()
                    ->toggleable(),

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

    public static function getNavigationItems(): array
    {
        $items = parent::getNavigationItems();

        $items[] = NavigationItem::make('Laporan Penjualan Kategori')
            ->url(static::getUrl('laporan'))
            ->icon('heroicon-o-chart-bar')
            ->visible(function () {
                $user = auth()->guard('pegawai')->user();
                return $user && $user->jabatan === 'Owner';
            });

        return $items;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKategoribarangs::route('/'),
            'create' => Pages\CreateKategoribarang::route('/create'),
            'edit' => Pages\EditKategoribarang::route('/{record}/edit'),
            'laporan' => Pages\LaporanKategori::route('/laporan'),
        ];
    }
}
