<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

abstract class SafeExcelValueBinder extends DefaultValueBinder
    implements WithCustomValueBinder
{
    public function bindValue(Cell $cell, $value): bool
    {
        // Nama, nomor identitas, nomor HP, dan catatan
        // selalu disimpan sebagai teks.
        if (is_string($value)) {
            $cell->setValueExplicit(
                $value,
                DataType::TYPE_STRING
            );

            return true;
        }

        // Nilai stok dan harga tetap numerik.
        return parent::bindValue($cell, $value);
    }
}
