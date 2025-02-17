<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    use HasFactory;

    protected $fillable = ['topic', 'description', 'password'];

    public function chats()
    {
        return $this->hasMany(ChatInForum::class);
    }

    public function forumable()
    {
        return $this->morphTo();
    }

    
}
