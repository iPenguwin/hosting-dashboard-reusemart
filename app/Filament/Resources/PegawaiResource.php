<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PegawaiResource\Pages;
use App\Models\Jabatan;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Pegawai';

    public static ?string $label = 'Pegawai';

    protected static ?string $navigationGroup = 'User';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('ID_JABATAN')
                    ->label('Jabatan')
                    ->options(Jabatan::all()->pluck('NAMA_JABATAN', 'ID_JABATAN'))
                    ->searchable()
                    ->preload(),

                TextInput::make('NAMA_PEGAWAI')
                    ->required()
                    ->label('Nama Pegawai')
                    ->placeholder('Masukkan Nama Pegawai')
                    ->maxLength(255),

                TextInput::make('NO_TELP_PEGAWAI')
                    ->required()
                    ->mask('+62 9999-9999-99999')
                    ->label('No Telepon Pegawai')
                    ->placeholder('Masukkan No Telepon Pegawai')
                    ->maxLength(255),

                TextInput::make('EMAIL_PEGAWAI')
                    ->required()
                    ->label('Email Pegawai')
                    ->email()
                    ->placeholder('Masukkan Email Pegawai')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('PASSWORD_PEGAWAI')
                    ->required(fn($operation) => $operation === 'create')
                    ->hidden(fn($operation) => $operation === 'edit')
                    ->label('Password Pegawai')
                    ->password()
                    ->revealable()
                    ->placeholder('Masukkan Password Pegawai')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->dehydrateStateUsing(
                        fn($state) =>
                        !password_get_info($state)['algo'] ? bcrypt($state) : $state
                    )
                    ->dehydrated(fn($state) => ! blank($state))
                    ->minLength(8)
                    ->maxLength(255),

                DatePicker::make('TGL_LAHIR_PEGAWAI')
                    ->required()
                    ->native(false)
                    ->label('Tanggal Lahir')
                    ->maxDate(now())
                    ->placeholder('Masukkan Tanggal Lahir'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('NAMA_PEGAWAI')
                    ->searchable()
                    ->copyable()
                    ->sortable()
                    ->label('Nama Pegawai'),
                TextColumn::make('jabatans.NAMA_JABATAN')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('NO_TELP_PEGAWAI')
                    ->searchable()
                    ->copyable()
                    ->sortable()
                    ->label('No Telepon Pegawai'),
                TextColumn::make('EMAIL_PEGAWAI')
                    ->searchable()
                    ->copyable()
                    ->sortable()
                    ->label('Email Pegawai'),
                TextColumn::make('KOMISI_PEGAWAI')
                    ->searchable()
                    ->copyable()
                    ->sortable()
                    ->label('Komisi Pegawai'),
                TextColumn::make('TGL_LAHIR_PEGAWAI')
                    ->date('d-m-Y')
                    ->copyable()
                    ->searchable()
                    ->label('Tanggal Lahir')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading(fn(Pegawai $record) => 'Hapus Pegawai ' . $record->NAMA_PEGAWAI)
                    ->modalDescription(fn(Pegawai $record) => 'Apakah Anda yakin ingin menghapus pegawai ' . $record->NAMA_PEGAWAI . '?')
                    ->label('Hapus')
                    ->action(fn(Pegawai $record) => $record->delete()),
                Tables\Actions\Action::make('resetPassword')
                    ->label('Reset Password')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(fn(Pegawai $record) => 'Reset Password ' . $record->NAMA_PEGAWAI)
                    ->modalDescription(fn(Pegawai $record) => 'Password pegawai ' . $record->NAMA_PEGAWAI . ' akan direset ke tanggal lahir (format: ddmmyyyy).')
                    ->action(function (Pegawai $record) {
                        $tanggalLahir = \Carbon\Carbon::parse($record->TGL_LAHIR_PEGAWAI)->format('dmY');
                        $record->PASSWORD_PEGAWAI = bcrypt($tanggalLahir);
                        $record->save();

                        Notification::make()
                            ->title('Password berhasil direset')
                            ->body("Password pegawai {$record->NAMA_PEGAWAI} direset ke tanggal lahir: {$tanggalLahir}")
                            ->success()
                            ->send();
                    }),

                // Tables\Actions\Action::make('resetPassword')
                //     ->label('Reset Password')
                //     ->icon('heroicon-o-key')
                //     ->color('warning')
                //     ->requiresConfirmation()
                //     ->modalHeading('Reset Password Pegawai')
                //     ->modalDescription('Password akan direset ke 6 digit terakhir nomor HP pegawai')
                //     ->action(function (Pegawai $record) {
                //         // Ambil nomor HP dan hilangkan semua karakter non-angka
                //         $nomorHp = preg_replace('/\D/', '', $record->NO_HP_PEGAWAI);

                //         // Ambil 6 digit terakhir
                //         $passwordBaru = substr($nomorHp, -6);

                //         // Simpan password baru yang sudah di-hash
                //         $record->PASSWORD_PEGAWAI = bcrypt($passwordBaru);
                //         $record->save();

                //         Notification::make()
                //             ->title('Password berhasil direset')
                //             ->body("Password pegawai direset ke 6 digit terakhir nomor HP: {$passwordBaru}")
                //             ->success()
                //             ->send();
                //     }),

                Tables\Actions\Action::make('calculateKomisi')
                    ->label('Hitung Komisi')
                    ->icon('heroicon-o-calculator')
                    ->color('success')
                    ->action(function (Pegawai $record) {
                        if (strtolower($record->jabatans->NAMA_JABATAN) === 'hunter') {
                            $komisi = $record->komisis()->sum('NOMINAL_KOMISI');
                            $record->update(['KOMISI_PEGAWAI' => $komisi]);

                            Notification::make()
                                ->title('Komisi dihitung')
                                ->body("Komisi hunter: {$komisi}")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Hanya untuk Hunter')
                                ->body('Hanya pegawai dengan jabatan Hunter yang memiliki komisi')
                                ->warning()
                                ->send();
                        }
                    }),
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
            'index' => Pages\ListPegawais::route('/'),
            'create' => Pages\CreatePegawai::route('/create'),
            'edit' => Pages\EditPegawai::route('/{record}/edit'),
        ];
    }
}
