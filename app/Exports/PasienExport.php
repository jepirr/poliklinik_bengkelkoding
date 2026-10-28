<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PasienExport extends SafeExcelValueBinder
    implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function query(): Builder
    {
        return User::query()
            ->where('role', 'pasien')
            ->orderBy('nama');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Pasien',
            'No. Rekam Medis',
            'Email',
            'No. KTP',
            'No. HP',
            'Alamat',
        ];
    }

    public function map($pasien): array
    {
        return [
            $pasien->id,
            $pasien->nama,
            $pasien->no_rm ?? '',
            $pasien->email,
            $pasien->no_ktp ?? '',
            $pasien->no_hp ?? '',
            $pasien->alamat ?? '',
        ];
    }
}
