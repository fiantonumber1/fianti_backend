<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewMemoTimeline extends Model
{
    use HasFactory;
    protected $fillable = ['new_memo_id', 'infostatus', 'entertime'];

    public function newMemo()
    {
        return $this->belongsTo(NewMemo::class);
    }
}
