<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PembayaranLunas extends Notification
{
    use Queueable;

    public function __construct(
        public int $pembayaranId,
        public int $totalBiaya
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'judul' => 'Pembayaran Dikonfirmasi',
            'pesan' => 'Pembayaran pemeriksaan sebesar Rp ' .
                number_format($this->totalBiaya, 0, ',', '.') .
                ' telah diverifikasi oleh Admin dan dinyatakan lunas.',
            'jenis' => 'pembayaran_lunas',
            'id_pembayaran' => $this->pembayaranId,
        ];
    }
}
