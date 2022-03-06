<?php

namespace App\Models\Listing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingAuctionBidAuto extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'user_id',
        'max_auto_bid_amount',
        'is_email_reminder_on',
        'is_out_bid_notification_sent',
        'is_auto_bid_enable'
    ];

    protected $table = 'listing_auction_bid_auto';

}
