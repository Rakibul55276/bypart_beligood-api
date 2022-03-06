<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingSubCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'is_active',
        'categories_id'
    ];

    protected $table = 'listing_sub_categories';
}
