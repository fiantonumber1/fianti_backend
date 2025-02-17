<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JustiMemoFeedback extends Model
{
    use HasFactory;

    protected $table = 'justi_memo_feedback';

    protected $fillable = [
        'justi_memo_id',
        'pic',
        'author',
        'level',
        'email',
        'hasilreview',
        'sudahdibaca',
        'comment',
        'conditionoffile',
        'conditionoffile2',
    ];

    // Relasi dengan JustiMemo
    public function justiMemo()
    {
        return $this->belongsTo(JustiMemo::class, 'justi_memo_id');
    }

    // Relasi dengan User sebagai PIC (Person in Charge)
    public function picUser()
    {
        return $this->belongsTo(User::class, 'pic');
    }

    // Relasi dengan User sebagai author (tambahkan jika ada tabel users)
    public function authorUser()
    {
        return $this->belongsTo(User::class, 'author');
    }

    public function feedbacklevel()
    {
        return $this->belongsTo(Unit::class, 'level_id');
    }
    public function files()
    {
        return $this->morphMany(CollectFile::class, 'collectable');
    }
}
