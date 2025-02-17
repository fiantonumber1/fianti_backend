<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobticketStartedRevReason extends Model
{
    use HasFactory;

    protected $fillable = [
        'jobticket_started_rev_id',
        'rule',
        'reason',
    ];

    /**
     * Relasi ke model JobticketStartedRev
     */
    public function jobticketStartedRev()
    {
        return $this->belongsTo(JobticketStartedRev::class, 'jobticket_started_rev_id');
    }

    public function files()
    {
        return $this->morphMany(CollectFile::class, 'collectable');
    }
}
