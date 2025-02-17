<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectFile extends Model
{
    use HasFactory;

    protected $fillable = ['filename', 'link', 'collectable_id', 'collectable_type'];

    public function collectable()
    {
        return $this->morphTo();
    }
}
