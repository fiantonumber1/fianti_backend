<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KatalogKomat extends Model
{
    use HasFactory;

    protected $table = 'katalog_komat'; // Nama tabel eksplisit

    // Kolom-kolom yang bisa diisi
    protected $fillable = [
        'kodematerial',
        'deskripsi',
        'spesifikasi',
        'UoM',
        'stokUUekpedisi',
        'stokUUgudang',
        'stokprojectekpedisi',
        'stokprojectgudang',
    ];
}

