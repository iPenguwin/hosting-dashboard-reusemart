<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MerchandiseResource\Pages;
use App\Filament\Resources\MerchandiseResource\RelationManagers;
use App\Models\Merchandise;
use Dom\Text;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MerchandiseResource extends Resource
{
    protected static ?string $model = Merchandise::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Merchandise';

    public static ?string $label = 'Merchandise';

    protected static ?string $navigationGroup = 'Merchandise';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('NAMA_MERCHANDISE')
                    ->required()
                    ->label('Nama Merchandise')
                    ->placeholder('Masukkan Nama Merchandise')
                    ->maxLength(255),
                FileUpload::make('GAMBAR')
                    ->label('Gambar Merchandise')
                    ->directory('merchandise')
                    ->disk('public')
                    ->required()
                    ->image()
                    ->reorderable()
                    ->appendFiles()
                    ->imageEditor()
                    ->downloadable(),
                TextInput::make('POIN_DIBUTUHKAN')
                    ->required()
                    ->label('Poin Diperlukan')
                    ->placeholder('Masukkan Poin Diperlukan')
                    ->numeric()
                    ->mask('9999999999')
                    ->minValue(0)
                    ->maxLength(11),
                TextInput::make('JUMLAH')
                    ->required()
                    ->mask('9999999999')
                    ->label('Jumlah')
                    ->placeholder('Masukkan Jumlah')
                    ->numeric()
                    ->minValue(0)
                    ->maxLength(11)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ID_MERCHANDISE')
                    ->label('ID Merchandise')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('NAMA_MERCHANDISE')
                    ->label('Nama Merchandise')
                    ->sortable()
                    ->searchable(),
                ImageColumn::make('GAMBAR')
                    ->label('Foto')
                    ->disk('public')
                    ->circular()
                    ->height(60),
                TextColumn::make('POIN_DIBUTUHKAN')
                    ->label('Poin Diperlukan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('JUMLAH')
                    ->label('Jumlah')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
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
            'index' => Pages\ListMerchandises::route('/'),
            'create' => Pages\CreateMerchandise::route('/create'),
            'edit' => Pages\EditMerchandise::route('/{record}/edit'),
        ];
    }
}
