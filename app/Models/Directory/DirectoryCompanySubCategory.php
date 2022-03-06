<?php

namespace App\Models\Directory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectoryCompanySubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'dir_sub_category_id',
        'dir_company_info_id'
    ];

    protected $table = 'directory_com_to_sub_cat';
}
