<?php

namespace App\Models\Listing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingBumpIds extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_bump',
        'duration_of_bump',
        'bump_end_date',
        'listing_id'
    ];

    protected $table = 'listing_bump_ids';
}
