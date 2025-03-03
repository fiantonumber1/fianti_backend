<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectType extends Model
{
    use HasFactory;

    protected $fillable = ['title'];

    public function newreports()
    {
        return $this->hasMany(Newreport::class, 'proyek_type_id');
    }
}
