<?php

namespace App\Models\Listing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingAuctionBid extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'user_id',
        'bid_amount',
    ];

    protected $table = 'listing_auction_bid';
}
