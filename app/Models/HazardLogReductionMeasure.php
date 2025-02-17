<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HazardLogReductionMeasure extends Model
{
    use HasFactory;

    protected $fillable = [
        'hazard_log_id',
        'unit_name',
        'reduction_measure',
        'status',
        'reason'
    ];

    public function hazardLog()
    {
        return $this->belongsTo(HazardLog::class);
    }
    public function forums()
    {
        return $this->morphMany(Forum::class, 'forumable');
    }
}
