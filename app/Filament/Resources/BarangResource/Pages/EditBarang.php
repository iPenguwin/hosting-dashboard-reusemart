<?php

namespace App\Filament\Resources\BarangResource\Pages;

use App\Filament\Resources\BarangResource;
use App\Models\Barang;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditBarang extends EditRecord
{
    protected static string $resource = BarangResource::class;

    public function getHeading(): string | Htmlable
    {
        if (auth()->guard('penitip')->check()) {
            return __('Detail Barang Titipan');
        }
        return parent::getHeading();
    }

    public function getBreadcrumbs(): array
    {
        return [
            $this->getResource()::getUrl() => $this->getResource()::getBreadcrumb(),
            '#' => auth()->guard('penitip')->check() ? 'Detail' : 'Edit',
        ];
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        // For all users (including penitip)
        $actions[] = Action::make('cetakNotaBarang')
            ->label('Cetak Nota')
            ->icon('heroicon-o-printer')
            ->color('success')
            ->action(function (Barang $record) {
                $pdf = Pdf::loadView('pdf.nota_barang', [
                    'barang' => $record->load([
                        'penitip',
                        'detailTransaksiPenitipans.transaksiPenitipan.pegawaiTransaksiPenitipans.pegawai',
                        'pegawai'
                    ])
                ]);

                return response()->streamDownload(
                    fn() => print($pdf->output()),
                    "Nota_Barang_{$record->KODE_BARANG}.pdf"
                );
            });

        // Only for non-penitip users
        if (!auth()->guard('penitip')->check()) {
            $actions[] = Actions\DeleteAction::make();

            // Add perpanjang action for admin/pegawai if needed
            $actions[] = Action::make('perpanjang')
                ->label('Perpanjang')
                ->icon('heroicon-o-clock')
                ->visible(function (Barang $record) {
                    return $record->TGL_KELUAR &&
                        (Carbon::parse($record->TGL_KELUAR)->subDay()->isToday() ||
                            Carbon::parse($record->TGL_KELUAR)->isPast());
                })
                ->action(function (Barang $record) {
                    $isFirstExtension = $record->STATUS_BARANG !== 'Diperpanjang';
                    $newTglKeluar = Carbon::parse($record->TGL_KELUAR)->addDays(30);

                    if ($isFirstExtension) {
                        $record->update([
                            'TGL_KELUAR' => $newTglKeluar,
                            'STATUS_BARANG' => 'Diperpanjang'
                        ]);
                        Notification::make()
                            ->title('Berhasil diperpanjang 30 hari')
                            ->success()
                            ->send();
                    } else {
                        $record->update([
                            'TGL_KELUAR' => $newTglKeluar,
                            'STATUS_BARANG' => 'Tidak Terjual'
                        ]);
                        Notification::make()
                            ->title('Masa penitipan maksimal 60 hari tercapai, status diubah ke Tidak Terjual')
                            ->warning()
                            ->send();
                    }
                })
                ->requiresConfirmation()
                ->modalHeading('Perpanjang Masa Titip')
                ->modalDescription('Apakah Anda yakin ingin memperpanjang masa penitipan barang ini selama 30 hari?')
                ->modalSubmitActionLabel('Ya, Perpanjang');
        }

        return $actions;
    }

    protected function getFormActions(): array
    {
        if (auth()->guard('penitip')->check()) {
            return [
                // For penitip - only back button and konfirmasi pengambilan if applicable
                ...$this->getPenitipFormActions(),
                Action::make('back')
                    ->label(__('Kembali'))
                    ->color('gray')
                    ->url($this->getRedirectUrl()),
            ];
        }

        // For non-penitip users
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getPenitipFormActions(): array
    {
        $actions = [];

        // Only show konfirmasi pengambilan if conditions are met
        if ($this->record->STATUS_BARANG === 'Tidak Terjual' && is_null($this->record->TGL_AMBIL)) {
            $actions[] = Action::make('konfirmasi_pengambilan')
                ->label('Konfirmasi Pengambilan')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->form([
                    DatePicker::make('TGL_AMBIL')
                        ->label('Tanggal Ambil')
                        ->required()
                        ->native(false)
                        ->default(now()),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'TGL_AMBIL' => $data['TGL_AMBIL'],
                        'STATUS_BARANG' => 'Tidak Tersedia'
                    ]);
                    Notification::make()
                        ->title('Pengambilan barang berhasil dikonfirmasi')
                        ->success()
                        ->send();
                    $this->redirect($this->getRedirectUrl());
                })
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Pengambilan Barang')
                ->modalDescription('Silakan masukkan tanggal pengambilan barang dan konfirmasi')
                ->modalSubmitActionLabel('Ya, Konfirmasi');
        }

        return $actions;
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->submit(null)
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Perubahan Data')
            ->modalDescription('Apakah Anda yakin ingin menyimpan perubahan data barang ini?')
            ->modalSubmitActionLabel('Ya, Simpan')
            ->modalCancelActionLabel('Batal')
            ->action(function () {
                $this->closeActionModal();
                $this->save();
            });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
