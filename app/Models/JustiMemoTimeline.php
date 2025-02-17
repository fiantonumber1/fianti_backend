<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JustiMemoTimeline extends Model
{
    use HasFactory;
    protected $fillable = ['justi_memo_id', 'infostatus', 'entertime'];

    public function justiMemo()
    {
        return $this->belongsTo(JustiMemo::class);
    }
}
