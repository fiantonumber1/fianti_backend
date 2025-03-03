<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewProgressReportDocumentKind extends Model
{
    use HasFactory;

    protected $table = 'newprogressreport_documentkind';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function progressReports()
    {
        return $this->hasMany(NewProgressReport::class, 'documentkind_id');
    }
    
}