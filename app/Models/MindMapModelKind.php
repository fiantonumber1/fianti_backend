<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MindMapModelKind extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description'];

    public function mindMaps()
    {
        return $this->hasMany(MindMapModel::class, 'level');
    }
}
