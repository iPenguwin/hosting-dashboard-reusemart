<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BadgeResource\Pages;
use App\Filament\Resources\BadgeResource\RelationManagers;
use App\Models\Badge;
use App\Models\Penitip;
use Dom\Text;
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

class BadgeResource extends Resource
{
    protected static ?string $model = Badge::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $navigationLabel = 'Badge';

    public static ?string $label = 'Badge';

    protected static ?string $navigationGroup = 'User';

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
                TextInput::make('NAMA_BADGE')
                    ->required()
                    ->label('Nama Badge')
                    ->placeholder('Masukkan Nama Badge')
                    ->maxLength(255),
                DatePicker::make('START_DATE')
                    ->required()
                    ->label('Tanggal Mulai')
                    ->placeholder('Pilih Tanggal Mulai')
                    ->maxDate(now()),
                DatePicker::make('END_DATE')
                    ->required()
                    ->label('Tanggal Berakhir')
                    ->placeholder('Pilih Tanggal Berakhir')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ID_BADGE')
                    ->label('ID Badge')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('penitips.NAMA_PENITIP')
                    ->label('Nama Penitip')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('NAMA_BADGE')
                    ->label('Nama Badge')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('START_DATE')
                    ->label('Tanggal Mulai')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('END_DATE')
                    ->label('Tanggal Berakhir')
                    ->date()
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Badge $record) {
                        $record->delete();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Badge')
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
            'index' => Pages\ListBadges::route('/'),
            'create' => Pages\CreateBadge::route('/create'),
            'edit' => Pages\EditBadge::route('/{record}/edit'),
        ];
    }
}
