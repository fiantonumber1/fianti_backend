<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revision extends Model
{
    use HasFactory;

    protected $fillable = [
        'newprogressreport_id',
        'revisionname',
        'start_time_run',
        'end_time_run',
        'revision_status',
        'total_elapsed_seconds',
    ];

    /**
     * Get the progress report associated with the revision.
     */
    public function newprogressreport()
    {
        return $this->belongsTo(Newprogressreport::class);
    }
}
