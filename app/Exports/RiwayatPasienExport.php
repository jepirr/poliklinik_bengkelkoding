<?php

namespace App\Exports;

use App\Models\Periksa;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class RiwayatPasienExport extends SafeExcelValueBinder
    implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        private int $dokterId
    ) {}

    public function query(): Builder
    {
        return Periksa::query()
            ->whereHas(
                'daftarPoli.jadwalPeriksa',
                function ($query) {
                    $query->where(
                        'id_dokter',
                        $this->dokterId
                    );
                }
            )
            ->with([
                'daftarPoli.pasien',
                'daftarPoli.jadwalPeriksa.dokter.poli',
                'detailPeriksas.obat',
            ])
            ->orderByDesc('tgl_periksa');
    }

    public function headings(): array
    {
        return [
            'ID Pemeriksaan',
            'Tanggal Periksa',
            'Nama Pasien',
            'Nomor Antrean',
            'Poli',
            'Keluhan',
            'Catatan Dokter',
            'Obat',
            'Total Biaya (Rp)',
        ];
    }

    public function map($periksa): array
    {
        $pendaftaran = $periksa->daftarPoli;

        $daftarObat = $periksa->detailPeriksas
            ->map(function ($detail) {
                return $detail->obat?->nama_obat;
            })
            ->filter()
            ->implode(', ');

        return [
            $periksa->id,
            $periksa->tgl_periksa
                ? Carbon::parse($periksa->tgl_periksa)
                    ->format('d/m/Y H:i')
                : '',
            $pendaftaran?->pasien?->nama ?? '',
            $pendaftaran?->no_antrian,
            $pendaftaran?->jadwalPeriksa?->dokter
                ?->poli?->nama_poli ?? '',
            $pendaftaran?->keluhan ?? '',
            $periksa->catatan ?? '',
            $daftarObat,
            (int) $periksa->biaya_periksa,
        ];
    }
}
