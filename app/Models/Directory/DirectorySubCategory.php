<?php

namespace App\Models\Directory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectorySubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'sub_category_name',
        'sub_category_icon',
        'directory_category_id'
    ];

    protected $table = 'directory_sub_category';
}
