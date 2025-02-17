<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagnosisResult extends Model
{
    protected $fillable = [
        'image_path',
        'user_id',
        'svm_prediction',
        'naive_bayes_prediction',
        'final_diagnosis',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
