<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobticketIdentity extends Model
{
    use HasFactory;

    protected $table = 'jobticket_identity';

    protected $fillable = [
        'jobticket_part_id',
        'documentnumber',
        'jobticket_documentkind_id',
        'newprogressreportids',
    ];

    public function jobticketPart()
    {
        return $this->belongsTo(JobticketPart::class, 'jobticket_part_id');
    }

    public function jobticketDocumentkind()
    {
        return $this->belongsTo(JobticketDocumentKind::class, 'jobticket_documentkind_id');
    }

    public function jobtickets()
    {
        return $this->hasMany(Jobticket::class, 'jobticket_identity_id');
    }

    public function jobticketHistories()
    {
        return $this->hasMany(JobticketHistory::class, 'jobticket_identity_id');
    }
}
