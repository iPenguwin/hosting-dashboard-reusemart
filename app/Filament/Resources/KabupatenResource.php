<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KabupatenResource\Pages;
use App\Filament\Resources\KabupatenResource\RelationManagers;
use App\Models\Kabupaten;
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

class KabupatenResource extends Resource
{
    protected static ?string $model = Kabupaten::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Domisili';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('id_provinsi')
                    ->label('Provinsi')
                    ->placeholder('Pilih Provinsi')
                    ->relationship('provinsi', 'nama_provinsi')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false),

                TextInput::make('nama_kabupaten_kota')
                    ->label('Nama Kabupaten/Kota')
                    ->placeholder('Masukkan Nama Kabupaten')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_kabupaten_kota')
                    ->label('Nama Kabupaten')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Kabupaten $record) {
                        $record->delete();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Kabupaten')
                    ->label('Delete')
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
            'index' => Pages\ListKabupatens::route('/'),
            'create' => Pages\CreateKabupaten::route('/create'),
            'edit' => Pages\EditKabupaten::route('/{record}/edit'),
        ];
    }
}
