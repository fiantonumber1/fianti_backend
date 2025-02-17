<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newbomkomathistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'newbomkomat_id', // Reference to newbomkomats
        'kodematerial',
        'material',
        'status',
        'rev',
    ];

    // Define the relationship with Newbomkomat
    public function newbomkomat()
    {
        return $this->belongsTo(Newbomkomat::class, 'newbomkomat_id');
    }
}
