<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiDonasiResource\Pages;
use App\Filament\Resources\TransaksiDonasiResource\RelationManagers;
use App\Models\Organisasi;
use App\Models\Request;
use App\Models\TransaksiDonasi;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class TransaksiDonasiResource extends Resource
{
    protected static ?string $model = TransaksiDonasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Riwayat Request Donasi Barang';

    public static ?string $label = 'Riwayat Request Donasi Barang';

    protected static ?string $navigationGroup = 'Organisasi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('ID_ORGANISASI')
                    ->default(function () {
                        return Auth::user()->ID_ORGANISASI ?? null;
                    }),

                Select::make('ID_REQUEST')
                    ->label('Request Barang')
                    ->options(function () {
                        $orgId = Auth::user()->ID_ORGANISASI ?? null;
                        return Request::with('barang')
                            ->where('ID_ORGANISASI', $orgId)
                            ->where('STATUS_REQUEST', 'Diterima')
                            ->whereDoesntHave('transaksiDonasis')
                            ->get()
                            ->mapWithKeys(function ($request) {
                                return [
                                    $request->ID_REQUEST => $request->barang->NAMA_BARANG ?? $request->NAMA_BARANG_REQUEST,
                                ];
                            });
                    })
                    ->required()
                    ->searchable()
                    ->preload(),

                DatePicker::make('TGL_DONASI')
                    ->label('Tanggal Donasi')
                    ->required()
                    ->default(now()),

                TextInput::make('PENERIMA')
                    ->required()
                    ->label('Nama Penerima'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ID_TRANSAKSI_DONASI')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('request.organisasi.NAMA_ORGANISASI')
                    ->label('Organisasi Penerima')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('request.barang.NAMA_BARANG')
                    ->label('Barang Didonasikan')
                    ->sortable()
                    ->searchable()
                    ->default(fn(TransaksiDonasi $record) => $record->request->NAMA_BARANG_REQUEST),

                TextColumn::make('TGL_DONASI')
                    ->label('Tanggal Donasi')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('PENERIMA')
                    ->label('Penerima')
                    ->placeholder('N/A')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('inputPenerima')
                    ->label('Input Penerima')
                    ->visible(
                        fn(TransaksiDonasi $record): bool =>
                        empty($record->PENERIMA) &&
                            Auth::user()->JABATAN === 'Owner'
                    )
                    ->icon('heroicon-o-user')
                    ->color('success')
                    ->modalHeading('Input Penerima Donasi')
                    ->form([
                        DatePicker::make('TGL_DONASI')
                            ->label('Tanggal Donasi')
                            ->required()
                            ->native(false)
                            ->default(now()),
                        TextInput::make('PENERIMA')
                            ->required()
                            ->placeholder('Masukkan Penerima')
                            ->label('Nama Penerima'),
                    ])
                    ->action(function (TransaksiDonasi $record, array $data): void {
                        $record->update([
                            'TGL_DONASI' => $data['TGL_DONASI'],
                            'PENERIMA' => $data['PENERIMA'],
                        ]);
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (TransaksiDonasi $record) {
                        $record->delete();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Hapus')
                    ->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if ($user instanceof \App\Models\Organisasi) {
            return parent::getEloquentQuery()
                ->whereHas('request', function ($query) use ($user) {
                    $query->where('ID_ORGANISASI', $user->ID_ORGANISASI);
                });
        }

        return parent::getEloquentQuery();
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
            'index' => Pages\ListTransaksiDonasis::route('/'),
            'create' => Pages\CreateTransaksiDonasi::route('/create'),
            'edit' => Pages\EditTransaksiDonasi::route('/{record}/edit'),
        ];
    }
}
