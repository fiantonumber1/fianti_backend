<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagnosisResult extends Model
{
    protected $fillable = [
        'image_path',
        'user_id',
        'cnn_prediction',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
