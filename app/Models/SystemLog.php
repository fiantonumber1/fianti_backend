<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    use HasFactory;

    protected $fillable = ['message', 'level', 'user', 'aksi','user_id'];

    public function loggable()
    {
        return $this->morphTo();
    }
}
