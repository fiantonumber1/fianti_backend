<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HazardLogFeedback extends Model
{
    use HasFactory;

    protected $table = 'hazardlogfeedbacks';

    protected $fillable = [
        'hazard_logs_id', 'pic', 'level', 'email', 'comment', 
        'conditionoffile', 'conditionoffile2'
    ];

    public function hazardLog()
    {
        return $this->belongsTo(HazardLog::class);
    }
    public function hazardLogFiles()
    {
        return $this->morphMany(CollectFile::class, 'collectable');
    }
}

