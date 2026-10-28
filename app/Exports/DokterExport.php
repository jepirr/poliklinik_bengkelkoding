<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DokterExport extends SafeExcelValueBinder
    implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function query(): Builder
    {
        return User::query()
            ->with('poli')
            ->where('role', 'dokter')
            ->orderBy('nama');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Dokter',
            'Email',
            'No. KTP',
            'No. HP',
            'Alamat',
            'Poli',
        ];
    }

    public function map($dokter): array
    {
        return [
            $dokter->id,
            $dokter->nama,
            $dokter->email,
            $dokter->no_ktp ?? '',
            $dokter->no_hp ?? '',
            $dokter->alamat ?? '',
            $dokter->poli?->nama_poli ?? '',
        ];
    }
}
