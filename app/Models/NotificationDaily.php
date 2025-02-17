<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationDaily extends Model
{
    use HasFactory;

    protected $table = 'notification_dailies';

    // Specify the attributes that are mass assignable
    protected $fillable = ['name'];
}
