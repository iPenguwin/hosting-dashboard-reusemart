<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenitipResource\Pages;
use App\Filament\Resources\PenitipResource\RelationManagers;
use App\Models\Penitip;
use Dom\Text;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PenitipResource extends Resource
{
    protected static ?string $model = Penitip::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Penitip';

    protected static ?string $navigationGroup = 'User';

    protected static ?string $slug = 'managemen-penitip';

    public static ?string $label = 'Penitip';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('NAMA_PENITIP')
                    ->required()
                    ->label('Nama Penitip')
                    ->placeholder('Masukkan Nama Penitip')
                    ->maxLength(255),

                TextInput::make('NO_KTP')
                    ->required()
                    ->mask('999999999999999999999999')
                    ->numeric()
                    ->label('NIK')
                    ->placeholder('Masukkan NIK')
                    ->maxLength(255),

                FileUpload::make('FOTO_NIK')
                    ->required()
                    ->directory('barang')
                    ->label('Foto KTP'),

                TextInput::make('ALAMAT_PENITIP')
                    ->required()
                    ->label('Alamat')
                    ->placeholder('Masukkan Alamat')
                    ->maxLength(255),

                DatePicker::make('TGL_LAHIR_PENITIP')
                    ->required()
                    ->native(false)
                    ->label('Tanggal Lahir')
                    ->placeholder('Masukkan Tanggal Lahir')
                    ->maxDate(now()),

                TextInput::make('NO_TELP_PENITIP')
                    ->required()
                    ->label('No HP')
                    ->mask('+62 9999-9999-99999')
                    ->placeholder('Masukkan No Telpon'),

                TextInput::make('EMAIL_PENITIP')
                    ->required()
                    ->label('Email')
                    ->email()
                    ->placeholder('Masukkan Email')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('PASSWORD_PENITIP')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->placeholder('Masukkan Password Penitip')
                    ->maxLength(255)
                    ->dehydrateStateUsing(
                        fn($state) =>
                        !password_get_info($state)['algo'] ? bcrypt($state) : $state
                    )
                    ->dehydrated(fn($state) => ! blank($state))
                    ->minLength(8)
                    ->maxLength(255)
                    // ->disabled(fn($operation) => $operation === 'edit')
                    // ->hidden(fn($operation) => $operation === 'edit')
                    ->hidden(fn($operation) => $operation === 'edit')
                    ->required(fn($operation) => $operation === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('NAMA_PENITIP')
                    ->searchable()
                    ->copyable()
                    ->label('Nama Penitip')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('NO_KTP')
                    ->searchable()
                    ->label('NIK')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('NO_TELP_PENITIP')
                    ->searchable()
                    ->label('No HP')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('ALAMAT_PENITIP')
                    ->searchable()
                    ->label('Alamat')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('EMAIL_PENITIP')
                    ->searchable()
                    ->copyable()
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->sortable()
                    ->dateTime()
                    ->dateTime('d-m-Y H:i:s'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Penitip $record) {
                        $record->delete();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Pegawai')
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
            'index' => Pages\ListPenitips::route('/'),
            'create' => Pages\CreatePenitip::route('/create'),
            'edit' => Pages\EditPenitip::route('/{record}/edit'),
        ];
    }
}
