<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiPenitipanBarangResource\Pages;
use App\Models\Penitip;
use App\Models\TransaksiPenitipanBarang;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Date;

class TransaksiPenitipanBarangResource extends Resource
{
    protected static ?string $model = TransaksiPenitipanBarang::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static ?string $label = 'Transaksi Penitipan Barang';

    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('ID_PENITIP')
                    ->label('Penitip')
                    ->options(Penitip::all()->pluck('NAMA_PENITIP', 'ID_PENITIP'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->columnSpanFull(),
                DatePicker::make('TGL_MASUK_TITIPAN')
                    ->required()
                    ->label('Tanggal Masuk Titipan')
                    ->placeholder('Pilih Tanggal Masuk Titipan'),
                DatePicker::make('TGL_KELUAR_TITIPAN')
                    ->required()
                    ->label('Tanggal Keluar Titipan')
                    ->placeholder('Pilih Tanggal Keluar Titipan'),
                TextInput::make('NO_NOTA_TRANSAKSI_TITIPAN')
                    ->label('No. Nota Transaksi')
                    ->disabled()
                    ->dehydrated()
                    ->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ID_TRANSAKSI_PENITIPAN')
                    ->label('ID Transaksi Penitipan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('penitip.NAMA_PENITIP')
                    ->label('Nama Penitip')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('TGL_MASUK_TITIPAN')
                    ->label('Tanggal Masuk Titipan')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('TGL_KELUAR_TITIPAN')
                    ->label('Tanggal Keluar Titipan')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('NO_NOTA_TRANSAKSI_TITIPAN')
                    ->label('No Nota')
                    ->sortable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('pdf')
                    ->label('Cetak Nota')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->action(function (TransaksiPenitipanBarang $record) {
                        $pdf = Pdf::loadView('pdf.nota_penitipan', [
                            'transaksi' => $record->load([
                                'penitip',
                                'detailTransaksiPenitipans.barang.pegawai',
                                'pegawaiTransaksiPenitipans.pegawai'
                            ])
                        ]);

                        return response()->streamDownload(
                            fn() => print($pdf->output()),
                            "Nota_Penitipan_{$record->NO_NOTA_TRANSAKSI_TITIPAN}.pdf"
                        );
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (TransaksiPenitipanBarang $record) {
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
            'index' => Pages\ListTransaksiPenitipanBarangs::route('/'),
            'create' => Pages\CreateTransaksiPenitipanBarang::route('/create'),
            'edit' => Pages\EditTransaksiPenitipanBarang::route('/{record}/edit'),
        ];
    }
}
