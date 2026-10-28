<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BuktiPembayaranDiunggah extends Notification
{
    use Queueable;

    public function __construct(
        public int $pembayaranId,
        public string $namaPasien
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'judul' => 'Bukti Pembayaran Baru',
            'pesan' => 'Pasien ' . $this->namaPasien .
                ' telah mengunggah bukti pembayaran untuk pemeriksaan Anda.',
            'jenis' => 'bukti_pembayaran',
            'id_pembayaran' => $this->pembayaranId,
        ];
    }
}
