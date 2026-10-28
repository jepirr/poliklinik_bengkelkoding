<?php

namespace App\Exports;

use App\Models\JadwalPeriksa;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class JadwalPeriksaExport extends SafeExcelValueBinder
    implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        private int $dokterId
    ) {}

    public function query(): Builder
    {
        return JadwalPeriksa::query()
            ->where('id_dokter', $this->dokterId)
            ->orderBy('id');
    }

    public function headings(): array
    {
        return [
            'ID Jadwal',
            'Hari',
            'Jam Mulai',
            'Jam Selesai',
            'Status',
        ];
    }

    public function map($jadwal): array
    {
        return [
            $jadwal->id,
            $jadwal->hari,
            substr($jadwal->jam_mulai, 0, 5),
            substr($jadwal->jam_selesai, 0, 5),
            $jadwal->status ? 'Aktif' : 'Tidak Aktif',
        ];
    }
}
