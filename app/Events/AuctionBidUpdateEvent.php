<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Listing\ListingAuctionBid;

class AuctionBidUpdateEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $listing_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($listing_id)
    {
        $this->listing_id = $listing_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('bid-update');
    }

    public function broadcastAs()
    {
        return 'AuctionBidUpdateEvent';
    }

    public function broadcastWith() {
        // $data = ListingAuctionBid::where('listing_id', $this->listing_id)
        // ->join('users', 'users.id', '=', 'listing_auction_bid.user_id')
        // ->orderBy('listing_auction_bid.bid_amount', 'desc')
        // ->get(['listing_auction_bid.id as auction_bid_id', 'listing_auction_bid.listing_id','users.id as user_id', 'users.first_name', 'users.last_name', 'listing_auction_bid.bid_amount', 'listing_auction_bid.created_at']);

        return [
          'data' => $this->listing_id,
        ];
      }
}
