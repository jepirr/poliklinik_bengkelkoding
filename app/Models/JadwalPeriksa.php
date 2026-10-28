<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalPeriksa extends Model
{
    // Nama tabel database
    protected $table = 'jadwal_periksa';

    // Kolom yang boleh diisi
    protected $fillable = [
        'id_dokter',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'status',
    ];

    // Konversi tipe data
    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    // Relasi jadwal dengan dokter
    public function dokter(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'id_dokter'
        );
    }

    // Relasi jadwal dengan pendaftaran poli
    public function daftarPolis(): HasMany
    {
        return $this->hasMany(
            DaftarPoli::class,
            'id_jadwal'
        );
    }
}
