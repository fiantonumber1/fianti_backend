<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        "title","excerpt",'body'
    ] ;
}


// Post::create(['title' => "judul4", 'slug'=>'judul4-beritaterbaru','excerpt' => "Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis quibusdam hic molestiae dicta, libero vel quas recusandae ratione illum omnis expedita soluta id deserunt?", 'body' => "Lorem, ipsum dolor sit amet consectetur adipisicing elit. Enim, magnam. Adipisci optio facere vel aliquam temporibus tempora dolor aut? Hic ut id obcaecati dolores dolorem maiores sint accusantium nisi labore, ex provident rerum, nihil natus deserunt amet nostrum facere quas dolore. Recusandae, culpa fugit? Laborum omnis eius earum."]);
