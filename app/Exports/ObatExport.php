<?php

namespace App\Exports;

use App\Models\Obat;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ObatExport extends SafeExcelValueBinder
    implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function query(): Builder
    {
        return Obat::query()
            ->orderBy('nama_obat');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Obat',
            'Kemasan',
            'Stok',
            'Harga (Rp)',
        ];
    }

    public function map($obat): array
    {
        return [
            $obat->id,
            $obat->nama_obat,
            $obat->kemasan ?? '',
            (int) $obat->stok,
            (int) $obat->harga,
        ];
    }
}
