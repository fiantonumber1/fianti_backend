<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileManagement extends Model
{
    use HasFactory;

    protected $table = 'file_management';
    
    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(ProjectType::class, 'project_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function files()
    {
        return $this->morphMany(CollectFile::class, 'collectable');
    }
}
