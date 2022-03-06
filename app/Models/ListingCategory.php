<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $table = 'listing_categories';
}
