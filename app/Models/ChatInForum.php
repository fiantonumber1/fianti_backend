<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatInForum extends Model
{
    use HasFactory;

    protected $fillable = ['forum_id', 'user_id', 'chat'];

    public function forum()
    {
        return $this->belongsTo(Forum::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chatFiles()
    {
        return $this->morphMany(CollectFile::class, 'collectable');
    }
}
