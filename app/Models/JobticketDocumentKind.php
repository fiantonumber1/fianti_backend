<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobticketDocumentKind extends Model
{
    use HasFactory;

    protected $table = 'jobticket_documentkind';

    protected $fillable = [
        'name',
        'description',
    ];

    public function jobtickets()
    {
        return $this->hasMany(Jobticket::class, 'jobticket_documentkind_id');
    }
}
