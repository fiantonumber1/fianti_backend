<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    // Jika Anda ingin menentukan kolom yang dapat diisi (mass assignable)
    protected $fillable = [
        'notifmessage_id',
        'notifmessage_type',
        'idunit',
        'status',
        'infostatus',
        'notifarray',
    ];

    /**
     * Mendefinisikan relasi polimorfik.
     */
    public function notifmessage()
    {
        return $this->morphTo();
    }

    /**
     * Relasi ke model Unit.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'idunit');
    }
    public function memo()
    {
        return $this->belongsTo(NewMemo::class, 'notifmessage_id');
    }
}
