<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;

class PenitipPickupNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $namaBarang,
        public string $tanggalAmbil,
        public int $barangId
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'message' => "Pembeli akan mengambil barang {$this->namaBarang} pada {$this->tanggalAmbil}",
            'barang_id' => $this->barangId,
            'action_url' => route('filament.penitip.resources.barangs.edit', $this->barangId),
            'action_label' => 'Lihat Barang',
        ];
    }

    public function shouldSend($notifiable, $channel): bool
    {
        return $channel === 'database';
    }
}
