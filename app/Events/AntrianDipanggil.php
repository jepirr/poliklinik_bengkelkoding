<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class AntrianDipanggil implements ShouldBroadcastNow
{
    public function __construct(
        public int $jadwalId,
        public ?int $nomorAntrian
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('antrian-poliklinik'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'antrian.dipanggil';
    }

    public function broadcastWith(): array
    {
        return [
            'jadwal_id' => $this->jadwalId,
            'no_antrian_dilayani' => $this->nomorAntrian,
        ];
    }
}
