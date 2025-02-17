<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobticketreason extends Model
{
    use HasFactory;

    protected $fillable = [
        'jobticket_id',
        'reason',
        'kind',
        'kind_id',
        'kind_type',
        'start',   // Tambahkan start di $fillable
        'end'      // Tambahkan end di $fillable
    ];

    // Relasi ke Jobticket
    public function jobticket()
    {
        return $this->belongsTo(Jobticket::class);
    }
    
    // Relasi ke CollectFile (jika ada relasi ini)
    public function collectFiles()
    {
        return $this->morphMany(CollectFile::class, 'collectable');
    }
}
