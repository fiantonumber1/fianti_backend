<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramMessage extends Model
{
    use HasFactory;

    protected $table = 'telegram_messages';

    protected $fillable = [
        'message_kind',
        'message',
        'array_message',
        'telegram_messages_accounts_id',
        'status',
        
    ];

    public function account()
    {
        return $this->belongsTo(TelegramMessagesAccount::class, 'telegram_messages_accounts_id');
    }
}
