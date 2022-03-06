<?php

namespace App\Models\Listing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_condition',
        'car_ownership_document',
        'car_plate_verification_image',
        'car_plate_number',
        'car_images',
        'state',
        'car_body_type',
        'car_make_name',
        'mileage',
        'model',
        'manufacture_year',
        'transmission',
        'fuel_type',
        'seats',
        'doors',
        'engine_size',
        'color',
        'listing_type',
        'ad_title',
        'ad_description',
        'area',
        'asking_price',
        'starting_price',
        'reserve_price',
        'buy_now_price',
        'duration_of_auction',
        'listing_status',
        'user_id',
        'start_date',
        'end_date',
        'variant',
        'is_feature',
        'feature_end_date',
        'is_highlight',
        'highlight_end_date',
        'categories_id',
        'list_relist_date'
    ];

    protected $casts = [
        'car_images' => 'array'
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'feature_end_date',
        'highlight_end_date'
    ];

    protected $table = 'listing';

    // So listing must need to belong to user
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    //Has many listing_question_answer
    public function listing_question_answer()
    {
        return $this->hasMany('App\Models\ListingQuestionAnswer')->limit(5);
    }

    /**
     * Has many listing_auction_bid
     * Although this hase many relation, we are using hasOne so that we only can get one result
     */

    public function listing_auction_bid()
    {
        return $this->hasOne('App\Models\Listing\ListingAuctionBid', 'listing_id')->latest();
    }

    public function listing_bump()
    {
        return $this->hasOne('App\Models\Listing\ListingBumpIds', 'listing_id')->latest("bump_end_date");
    }
}
