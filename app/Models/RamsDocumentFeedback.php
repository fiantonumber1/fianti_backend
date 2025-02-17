<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RamsDocumentFeedback extends Model
{
    use HasFactory;

    protected $table = 'rams_document_feedbacks'; // Ensure table name matches your migration

    protected $fillable = [
        'rams_document_id',
        'pic',
        'author',
        'level',
        'email',
        'comment',
        'conditionoffile',
        'conditionoffile2',
    ];

    /**
     * Get the document that owns the feedback.
     */
    public function document()
    {
        return $this->belongsTo(RamsDocument::class, 'rams_document_id');
    }
    public function feedbackfiles()
    {
        return $this->morphMany(CollectFile::class, 'collectable');
    }


    

}
