<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewMemoKomat extends Model
{
    protected $fillable = ['new_memo_id', 'kodematerial', 'material', 'supplier'];

    public function newMemo()
    {
        return $this->belongsTo(NewMemo::class);
    }
}
