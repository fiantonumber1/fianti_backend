<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newbomkomat extends Model
{
    use HasFactory;

    protected $fillable = ['newbom_id', 'kodematerial', 'material', 'status'];

    public function newbom()
    {
        return $this->belongsTo(Newbom::class);
    }

    public function newprogressreports()
    {
        return $this->belongsToMany(Newprogressreport::class, 'newbomkomat_newprogressreport', 'newbomkomat_id', 'newprogressreport_id')
            ->withTimestamps();
    }
}
