<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiskusiResource\Pages;
use App\Filament\Resources\DiskusiResource\RelationManagers;
use App\Models\Barang;
use App\Models\Diskusi;
use App\Models\DiskusiPegawai;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DiskusiResource extends Resource
{
    protected static ?string $model = Diskusi::class;

    protected static ?string $navigationLabel = 'Diskusi Barang';

    public static ?string $label = 'Diskusi Barang';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Barang';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('ID_BARANG')
                    ->label('Barang')
                    ->relationship('barang', 'NAMA_BARANG')
                    ->required(),

                Select::make('ID_PEMBELI')
                    ->label('Pembeli')
                    ->relationship('pembeli', 'NAMA_PEMBELI')
                    ->required(),

                Textarea::make('PERTANYAAN')
                    ->label('Pertanyaan')
                    ->required(),

                Select::make('ID_PEGAWAI')
                    ->label('Pegawai Penjawab')
                    ->relationship('pegawai', 'NAMA_PEGAWAI')
                    ->visible(fn($operation) => $operation === 'edit'),

                Textarea::make('JAWABAN')
                    ->label('Jawaban')
                ->visible(fn($operation) => $operation === 'edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ID_DISKUSI')
                    ->label('ID'),
                TextColumn::make('barang.NAMA_BARANG')
                    ->label('Barang'),
                TextColumn::make('pembeli.NAMA_PEMBELI')
                    ->label('Pembeli'),
                TextColumn::make('PERTANYAAN')
                    ->label('Pertanyaan')
                    ->wrap(),
                TextColumn::make('pegawai.NAMA_PEGAWAI')
                    ->label('Penjawab'),
                TextColumn::make('JAWABAN')
                    ->label('Jawaban')
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('jawab')
                    ->form([
                        Select::make('ID_PEGAWAI')
                            ->label('Pegawai')
                            ->options(Pegawai::all()->pluck('NAMA_PEGAWAI', 'ID_PEGAWAI'))
                            ->required(),
                        Textarea::make('JAWABAN')
                            ->label('Jawaban')
                            ->required(),
                    ])
                    ->action(function (Diskusi $record, array $data) {
                        $record->update([
                            'ID_PEGAWAI' => $data['ID_PEGAWAI'],
                            'JAWABAN' => $data['JAWABAN']
                        ]);

                        DiskusiPegawai::create([
                            'ID_DISKUSI' => $record->ID_DISKUSI,
                            'ID_PEGAWAI' => $data['ID_PEGAWAI']
                        ]);
                    })
                    ->visible(fn(Diskusi $record) => empty($record->JAWABAN)),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Diskusi $record) {
                        $record->delete();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Diskusi Barang')
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
            'index' => Pages\ListDiskusis::route('/'),
            'create' => Pages\CreateDiskusi::route('/create'),
            'edit' => Pages\EditDiskusi::route('/{record}/edit'),
        ];
    }
}
