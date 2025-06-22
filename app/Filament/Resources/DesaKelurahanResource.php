<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DesaKelurahanResource\Pages;
use App\Filament\Resources\DesaKelurahanResource\RelationManagers;
use App\Models\DesaKelurahan;
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

class DesaKelurahanResource extends Resource
{
    protected static ?string $model = DesaKelurahan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Domisili';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('id_kecamatan')
                    ->label('Kecamatan')
                    ->placeholder('Pilih Kecamatan')
                    ->relationship('kecamatan', 'nama_kecamatan')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false),

                TextInput::make('nama_desa_kelurahan')
                    ->label('Nama Desa/Kelurahan')
                    ->placeholder('Masukkan Nama Desa/Kelurahan')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_desa_kelurahan')
                    ->label('Nama Desa/Kelurahan')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (DesaKelurahan $record) {
                        $record->delete();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Desa/Kelurahan')
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
            'index' => Pages\ListDesaKelurahans::route('/'),
            'create' => Pages\CreateDesaKelurahan::route('/create'),
            'edit' => Pages\EditDesaKelurahan::route('/{record}/edit'),
        ];
    }
}
