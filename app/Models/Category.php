<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['category_name', 'category_member'];

    // Menentukan bahwa 'category_member' akan di-cast (diubah) menjadi array ketika diambil dari database
    protected $casts = [
        'category_member' => 'array',
    ];


    public static function getCategoryMemberByName($categoryName)
    {
        return Category::where('category_name', $categoryName)->pluck('category_member');
    }

    public static function getlistCategoryMemberByName($categoryName)
    {
        $category= Category::where('category_name', $categoryName)->pluck('category_member');
        $categoryprojectbaru = json_decode($category, true)[0];
        $categoryproject = trim($categoryprojectbaru, '"');
        $listpic = json_decode($categoryproject, true);
        return $listpic;
    }
}
