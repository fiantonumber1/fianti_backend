<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoomAccessToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_name',
        'zoom_clientid',
        'zoom_clientsecret',
        'zoom_redirecturl',
        'zoom_hotkey',
        'access_token',
        'refresh_token',
        'expires_at',
        'jenis',
        'account_expired'
    ];

    protected $dates = [
        'expires_at',
    ];

    public static function getAllAccountNames()
    {
        return self::pluck('account_name')->toArray();
    }
}
