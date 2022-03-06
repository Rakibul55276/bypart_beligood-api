<?php

namespace App\Models\Listing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingUserActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_favorite',
        'is_won',
        'is_lost',
        'is_bought',
        'buy_now_reference',
        'buy_time',
        'user_id',
        'listing_id'
    ];

    protected $table = 'listing_user_activity';
}
