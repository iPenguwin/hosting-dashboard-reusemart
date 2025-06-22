<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KlaimMerchandiseResource\Pages;
use App\Models\KlaimMerchandise;
use App\Models\Merchandise;
use App\Models\Pembeli;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;   
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KlaimMerchandiseResource extends Resource
{
    protected static ?string $model = KlaimMerchandise::class;

    protected static ?string $navigationLabel = 'Klaim Merchandise';
    public static    ?string $label           = 'Klaim Merchandise';
    protected static ?string $navigationIcon  = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Merchandise';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('ID_MERCHANDISE')
                    ->label('Nama Merchandise')
                    ->options(
                        Merchandise::all()->pluck('NAMA_MERCHANDISE', 'ID_MERCHANDISE')
                    )
                    ->searchable()
                    ->required()
                    ->preload(),

                Select::make('ID_PEMBELI')
                    ->label('Nama Pembeli')
                    ->options(
                        Pembeli::all()->pluck('NAMA_PEMBELI', 'ID_PEMBELI')
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                DatePicker::make('TGL_KLAIM')
                    ->label('Tanggal Klaim')
                    ->required(),

                DatePicker::make('TGL_PENGAMBILAN')
                    ->label('Tanggal Pengambilan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Nama Merchandise
                TextColumn::make('merchandise.NAMA_MERCHANDISE')
                    ->label('Nama Merchandise')
                    ->sortable()
                    ->searchable(),

                // Nama Pembeli
                TextColumn::make('pembeli.NAMA_PEMBELI')
                    ->label('Nama Pembeli')
                    ->sortable()
                    ->searchable(),

                // Tanggal Klaim
                TextColumn::make('TGL_KLAIM')
                    ->label('Tanggal Klaim')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

                // Kolom Status (menggunakan accessor getStatusAttribute)
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'danger'  => 'Belum Diambil', // Merah jika Belum Diambil
                        'success' => 'Sudah Diambil', // Hijau jika Sudah Diambil
                    ])
                    ->sortable(),

                // Kolom “Diambil Pada”
                TextColumn::make('TGL_PENGAMBILAN')
                    ->label('Diambil Pada')
                    ->formatStateUsing(fn (?string $state): string =>
                        $state
                            ? Carbon::parse($state)->format('d/m/Y')
                            : '—'
                    )
                    ->toggleable()
                    ->sortable(),
            ])
            ->filters([
                // ─────────────────────────────────────────────────────────────
                // Filter: “Hanya Belum Diambil” dengan toggle
                Filter::make('belum_diambil')
                    ->label('Belum Diambil')
                    ->toggle() // Menjadikan filter ini switch ON/OFF
                    ->query(fn (Builder $query) => $query->whereNull('TGL_PENGAMBILAN'))
                    ->default(false),
                // ─────────────────────────────────────────────────────────────
            ])
            ->actions([
                Tables\Actions\Action::make('markAsPicked')
                    ->label('Tandai Diambil')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pengambilan')
                    ->modalDescription('Yakin ingin menandai klaim ini sudah diambil?')
                    ->visible(fn (KlaimMerchandise $record): bool => is_null($record->TGL_PENGAMBILAN))
                    ->action(fn (KlaimMerchandise $record) =>
                        $record->update(['TGL_PENGAMBILAN' => now()])
                    ),

                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Klaim Merchandise'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulkMarkPicked')
                        ->label('Tandai Semua Diambil')
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Bulk Pengambilan')
                        ->modalDescription('Yakin ingin menandai semua klaim terpilih sudah diambil?')
                        ->action(fn ($records) =>
                            $records->each(fn (KlaimMerchandise $record) =>
                                is_null($record->TGL_PENGAMBILAN)
                                    ? $record->update(['TGL_PENGAMBILAN' => now()])
                                    : null
                            )
                        ),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListKlaimMerchandises::route('/'),
            'create' => Pages\CreateKlaimMerchandise::route('/create'),
            'edit'   => Pages\EditKlaimMerchandise::route('/{record}/edit'),
        ];
    }
}
