<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    protected $table = 'obat';

    protected $fillable = [
        'nama_obat',
        'kemasan',
        'stok',
        'harga',
    ];

    protected function casts(): array
    {
        return [
            'stok' => 'integer',
            'harga' => 'integer',
        ];
    }

    public function detailPeriksas()
    {
        return $this->hasMany(
            DetailPeriksa::class,
            'id_obat'
        );
    }
}
