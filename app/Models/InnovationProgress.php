<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InnovationProgress extends Model
{
    use HasFactory;

    protected $table = 'innovation_progress';

    protected $fillable = [
        'name',
        'description',
        'manual_book_link',
        'flow_chart_link',
        'documentation_link',
    ];

    protected $casts = [
        'name' => 'string',
        'description' => 'string',
        'manual_book_link' => 'string',
        'flow_chart_link' => 'string',
        'documentation_link' => 'string',
    ];
}
