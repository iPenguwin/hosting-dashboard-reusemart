<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganisasiResource\Pages;
use App\Filament\Resources\OrganisasiResource\RelationManagers;
use App\Models\Organisasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class OrganisasiResource extends Resource
{
    protected static ?string $model = Organisasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Organisasi';

    public static ?string $label = 'Organisasi';

    protected static ?string $navigationGroup = 'Organisasi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('NAMA_ORGANISASI')
                    ->required()
                    ->label('Nama Organisasi')
                    ->placeholder('Masukkan Nama Organisasi')
                    ->maxLength(255),

                TextInput::make('ALAMAT_ORGANISASI')
                    ->required()
                    ->label('Alamat Organisasi')
                    ->placeholder('Masukkan Alamat Organisasi')
                    ->maxLength(255),

                TextInput::make('NO_TELP_ORGANISASI')
                    ->required()
                    ->mask('999999999999999999')
                    ->label('No Telepon Organisasi')
                    ->placeholder('Masukkan No Telepon Organisasi')
                    ->maxLength(25),

                TextInput::make('EMAIL_ORGANISASI')
                    ->required()
                    ->label('Email Organisasi')
                    ->email()
                    ->placeholder('Masukkan Email Organisasi')
                    ->unique(ignoreRecord: true)
                    ->maxLength(25),

                TextInput::make('PASSWORD_ORGANISASI')
                    ->hidden(fn($operation) => $operation === 'edit')
                    ->required(fn($operation) => $operation === 'create')
                    ->label('Password Organisasi')
                    ->password()
                    ->revealable()
                    ->placeholder('Masukkan Password Organisasi')
                    ->maxLength(255)
                    ->dehydrateStateUsing(
                        fn($state) =>
                        !password_get_info($state)['algo'] ? bcrypt($state) : $state
                    )
                    ->dehydrated(fn($state) => ! blank($state))
                    ->minLength(8)
                    ->maxLength(255)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('NAMA_ORGANISASI')
                    ->searchable()
                    ->copyable()
                    ->label('Nama Organisasi')
                    ->sortable(),
                TextColumn::make('ALAMAT_ORGANISASI')
                    ->copyable()
                    ->searchable()
                    ->label('Alamat Organisasi'),
                TextColumn::make('NO_TELP_ORGANISASI')
                    ->copyable()
                    ->searchable()
                    ->label('No Telepon Organisasi'),
                TextColumn::make('EMAIL_ORGANISASI')
                    ->copyable()
                    ->searchable()
                    ->label('Email Organisasi'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Organisasi $record) {
                        $record->delete();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Pembeli')
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
            'index' => Pages\ListOrganisasis::route('/'),
            'create' => Pages\CreateOrganisasi::route('/create'),
            'edit' => Pages\EditOrganisasi::route('/{record}/edit'),
        ];
    }
}
