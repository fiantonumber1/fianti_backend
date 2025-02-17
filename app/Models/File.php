<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class File extends Model
{
    use HasFactory;
    protected $fillable = ['count','feedbacktimestamp','filename', 'metadata','author','linkfile','project_type','project_pic','category','documentname','comment'];


}
