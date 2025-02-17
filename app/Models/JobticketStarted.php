<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobticketStarted extends Model
{
    use HasFactory;

    protected $table = 'jobticket_started';

    protected $fillable = [
        'jobticket_id',
        'start_time_run',
        'pause_time_run',
        'total_elapsed_seconds',
        'revisionlast'
    ];

    public function jobticket()
    {
        return $this->belongsTo(Jobticket::class, 'jobticket_id');
    }

    public function revisions()
    {
        return $this->hasMany(JobticketStartedRev::class);
    }
}
