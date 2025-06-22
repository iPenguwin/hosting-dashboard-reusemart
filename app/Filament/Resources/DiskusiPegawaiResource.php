<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiskusiPegawaiResource\Pages;
use App\Filament\Resources\DiskusiPegawaiResource\RelationManagers;
use App\Models\Diskusi;
use App\Models\DiskusiPegawai;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DiskusiPegawaiResource extends Resource
{
    protected static ?string $model = DiskusiPegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Diskusi Pegawai';

    public static ?string $label = 'Diskusi Pegawai';

    protected static ?string $navigationGroup = 'Barang';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('ID_DISKUSI')
                    ->label('Diskusi')
                    ->options(Diskusi::all()->mapWithKeys(function ($item) {
                        return [$item->ID_DISKUSI => "Diskusi #{$item->ID_DISKUSI} - {$item->barang->NAMA_BARANG}"];
                    }))
                    ->required(),

                Select::make('ID_PEGAWAI')
                    ->label('Pegawai')
                    ->relationship('pegawai', 'NAMA_PEGAWAI')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('diskusi.barang.NAMA_BARANG')
                    ->label('Barang'),
                TextColumn::make('diskusi.PERTANYAAN')
                    ->label('Pertanyaan')->wrap(),
                TextColumn::make('pegawai.NAMA_PEGAWAI')
                    ->label('Pegawai'),
                TextColumn::make('diskusi.JAWABAN')
                    ->label('Jawaban')
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (DiskusiPegawai $record) {
                        $record->delete();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Diskusi Pegawai')
                    ->label('Apakah Anda yakin ingin menghapus diskusi ini?')
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
            'index' => Pages\ListDiskusiPegawais::route('/'),
            'create' => Pages\CreateDiskusiPegawai::route('/create'),
            'edit' => Pages\EditDiskusiPegawai::route('/{record}/edit'),
        ];
    }
}
