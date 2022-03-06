<?php

namespace App\Models\Directory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectoryCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_name',
        'category_icon'
    ];

    protected $table = 'directory_category';

    public static function subCategoryList($category_id){

        $query = DirectorySubCategory::where('directory_category_id',$category_id)->get();
        
        return $query;
    }
}
