<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembeliResource\Pages;
use App\Filament\Resources\PembeliResource\RelationManagers;
use App\Models\Pembeli;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PembeliResource extends Resource
{
    protected static ?string $model = Pembeli::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Pembeli';

    public static ?string $label = 'Pembeli';

    protected static ?string $navigationGroup = 'User';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('NAMA_PEMBELI')
                    ->required()
                    ->label('Nama Pembeli')
                    ->placeholder('Masukkan Nama Pembeli')
                    ->maxLength(255),

                DatePicker::make('TGL_LAHIR_PEMBELI')
                    ->required()
                    ->native(false)
                    ->maxDate(now())
                    ->label('Tanggal Lahir')
                    ->placeholder('Masukkan Tanggal Lahir')
                    ->date(),

                TextInput::make('NO_TELP_PEMBELI')
                    ->required()
                    ->label('No Telepon')
                    ->placeholder('Masukkan No Telepon')
                    ->tel()
                    ->mask('+62 9999-9999-99999')
                    ->maxLength(25),

                TextInput::make('EMAIL_PEMBELI')
                    ->required()
                    ->label('Email')
                    ->email()
                    ->placeholder('Masukkan Email')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('PASSWORD_PEMBELI')
                    ->hidden(fn($operation) => $operation === 'edit')
                    ->required(fn($operation) => $operation === 'create')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->placeholder('Masukkan Password')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->dehydrateStateUsing(
                        fn($state) =>
                        !password_get_info($state)['algo'] ? bcrypt($state) : $state
                    )
                    ->dehydrated(fn($state) => ! blank($state))
                    ->minLength(8)
                    ->maxLength(255),

                TextInput::make('POINT_LOYALITAS_PEMBELI')
                    ->disabled()
                    ->label('Point Loyalitas')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('NAMA_PEMBELI')
                    ->searchable()
                    ->copyable()
                    ->label('Nama Pembeli')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('TGL_LAHIR_PEMBELI')
                    ->label('Tanggal Lahir')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('NO_TELP_PEMBELI')
                    ->copyable()
                    ->searchable()
                    ->label('No Telepon')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('EMAIL_PEMBELI')
                    ->copyable()
                    ->searchable()
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('POINT_LOYALITAS_PEMBELI')
                    ->label('Point Loyalitas')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Pembeli $record) {
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
            'index' => Pages\ListPembelis::route('/'),
            'create' => Pages\CreatePembeli::route('/create'),
            'edit' => Pages\EditPembeli::route('/{record}/edit'),
        ];
    }
}
