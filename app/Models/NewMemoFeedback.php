<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewMemoFeedback extends Model
{
    protected $fillable = [
        'new_memo_id', 'pic', 'author', 'level', 'email','sudahdibaca','hasilreview', 'comment', 'conditionoffile', 'conditionoffile2'
    ];

    public function newMemo()
    {
        return $this->belongsTo(NewMemo::class);
    }

    public function files()
    {
        return $this->morphMany(CollectFile::class, 'collectable');
    }
}
