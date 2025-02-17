<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotifHarianUnit extends Model
{
    protected $table = 'notif_harian_units';

    // Allow mass assignment for these fields
    protected $fillable = ['title', 'documentkind', 'telegrammessagesaccount_id'];

    // Cast documentkind field as JSON
    protected $casts = [
        'documentkind' => 'array', // Automatically cast JSON to array
    ];

    // Define the relationship with TelegramMessagesAccount
    public function telegrammessagesaccount()
    {
        return $this->belongsTo(TelegramMessagesAccount::class, 'telegrammessagesaccount_id');
    }
}


