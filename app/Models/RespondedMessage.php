<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespondedMessage extends Model
{
    use HasFactory;
    protected $fillable = [
        'message_id',
        'chat_id',
        'date',
    ];
}
