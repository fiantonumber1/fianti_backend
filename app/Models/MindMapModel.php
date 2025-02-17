<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MindMapModel extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'parent_id', 'level'];

    // Relasi dengan parent node
    public function parent()
    {
        return $this->belongsTo(MindMapModel::class, 'parent_id');
    }

    // Relasi dengan child nodes
    public function children()
    {
        return $this->hasMany(MindMapModel::class, 'parent_id');
    }
    public function kind()
    {
        return $this->belongsTo(MindMapModelKind::class, 'level');
    }
}
