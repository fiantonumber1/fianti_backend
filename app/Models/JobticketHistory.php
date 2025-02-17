<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobticketHistory extends Model
{
    use HasFactory;

    protected $table = 'jobticket_history'; // Nama tabel di database

    protected $fillable = [
        'historykind',
        'jobticket_identity_id',
        'newprogressreporthistory_id',
        'newprogressreport_id',
        'status',
        'description',
    ];

    // Relasi ke model Jobticket
    public function jobticket_identity()
    {
        return $this->belongsTo(JobticketIdentity::class, 'jobticket_identity_id');
    }

    // Relasi ke model Newprogressreporthistory
    public function newprogressreporthistory()
    {
        return $this->belongsTo(Newprogressreporthistory::class, 'newprogressreporthistory_id');
    }

    // Relasi ke model Newprogressreport
    public function newprogressreport()
    {
        return $this->belongsTo(Newprogressreport::class, 'newprogressreport_id');
    }

     // Relasi ke model JobticketHistory
     public function jobticketHistories()
     {
         return $this->hasMany(JobticketHistory::class, 'jobticket_id');
     }
}
