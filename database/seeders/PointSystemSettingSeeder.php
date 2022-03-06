<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PointSystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Point system for private account type
        DB::table('user_point_setting')->insert([
            'id' => 1,
            "point_category" => "private",
            "transaction_type" => "classified_listing_price",
            'deduction_point' => '0',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 2,
            "point_category" => "private",
            "transaction_type" => "auction_listing_price",
            'deduction_point' => '0',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 3,
            "point_category" => "private",
            "transaction_type" => "edit_fee",
            'deduction_point' => '5',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 4,
            "point_category" => "private",
            "transaction_type" => "switch_classified_to_auction",
            'deduction_point' => '10',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 5,
            "point_category" => "private",
            "transaction_type" => "switch_auction_to_classified",
            'deduction_point' => '10',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 6,
            "point_category" => "private",
            "transaction_type" => "withdraw_fee_reserve_not_meet",
            'deduction_point' => '20',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 7,
            "point_category" => "private",
            "transaction_type" => "withdraw_fee_reserve_meet",
            'deduction_point' => '200',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 8,
            "point_category" => "private",
            "transaction_type" => "bump_refresh",
            'deduction_point' => '5',
        ]);

        DB::table('user_point_setting')->insert([
            'id' =>9,
            "point_category" => "private",
            "transaction_type" => "bump_refresh_7_days",
            'deduction_point' => '20',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 10,
            "point_category" => "private",
            "transaction_type" => "feature_7_days",
            'deduction_point' => '30',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 11,
            "point_category" => "private",
            "transaction_type" => "feature_14_days",
            'deduction_point' => '50',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 12,
            "point_category" => "private",
            "transaction_type" => "highlight_ads",
            'deduction_point' => '15',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 13,
            "point_category" => "private",
            "transaction_type" => "relist_ad",
            'deduction_point' => '5',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 14,
            "point_category" => "private",
            "transaction_type" => "auction_winner_success_fee",
            'deduction_point' => '200',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 15,
            "point_category" => "private",
            "transaction_type" => "buy_decline_the_bid",
            'deduction_point' => '200',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 16,
            "point_category" => "private",
            "transaction_type" => "check_seller_info",
            'deduction_point' => '10',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 17,
            "point_category" => "private",
            "transaction_type" => "check_buyer_info_by_seller",
            'deduction_point' => '5',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 18,
            "point_category" => "private",
            "transaction_type" => "success_fee",
            'deduction_point' => '200',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 19,
            "point_category" => "private",
            "transaction_type" => "new_auction_listing_lock_amount",
            'deduction_point' => '200',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 20,
            "point_category" => "private",
            "transaction_type" => "high_bidder_lock_amount",
            'deduction_point' => '200',
        ]);


        // Point system for broker/sales agent
        // Let resever id until 30, broker_agent
        DB::table('user_point_setting')->insert([
            'id' => 30,
            "point_category" => "broker_agent",
            "transaction_type" => "classified_listing_price",
            'deduction_point' => '0',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 31,
            "point_category" => "broker_agent",
            "transaction_type" => "auction_listing_price",
            'deduction_point' => '0',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 32,
            "point_category" => "broker_agent",
            "transaction_type" => "edit_fee",
            'deduction_point' => '5',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 33,
            "point_category" => "broker_agent",
            "transaction_type" => "switch_classified_to_auction",
            'deduction_point' => '10',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 34,
            "point_category" => "broker_agent",
            "transaction_type" => "switch_auction_to_classified",
            'deduction_point' => '10',
        ]);

        DB::table('user_point_setting')->insert([
            'id' =>35,
            "point_category" => "broker_agent",
            "transaction_type" => "withdraw_fee_reserve_not_meet",
            'deduction_point' => '20',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 36,
            "point_category" => "broker_agent",
            "transaction_type" => "withdraw_fee_reserve_meet",
            'deduction_point' => '200',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 37,
            "point_category" => "broker_agent",
            "transaction_type" => "bump_refresh",
            'deduction_point' => '5',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 38,
            "point_category" => "broker_agent",
            "transaction_type" => "bump_refresh_7_days",
            'deduction_point' => '20',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 39,
            "point_category" => "broker_agent",
            "transaction_type" => "feature_7_days",
            'deduction_point' => '30',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 40,
            "point_category" => "broker_agent",
            "transaction_type" => "feature_14_days",
            'deduction_point' => '50',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 41,
            "point_category" => "broker_agent",
            "transaction_type" => "highlight_ads",
            'deduction_point' => '15',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 42,
            "point_category" => "broker_agent",
            "transaction_type" => "relist_ad",
            'deduction_point' => '5',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 43,
            "point_category" => "broker_agent",
            "transaction_type" => "auction_winner_success_fee",
            'deduction_point' => '200',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 44,
            "point_category" => "broker_agent",
            "transaction_type" => "buy_decline_the_bid",
            'deduction_point' => '200',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 45,
            "point_category" => "broker_agent",
            "transaction_type" => "check_seller_info",
            'deduction_point' => '10',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 46,
            "point_category" => "broker_agent",
            "transaction_type" => "check_buyer_info_by_seller",
            'deduction_point' => '5',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 47,
            "point_category" => "broker_agent",
            "transaction_type" => "success_fee",
            'deduction_point' => '200',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 48,
            "point_category" => "broker_agent",
            "transaction_type" => "new_auction_listing_lock_amount",
            'deduction_point' => '500',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 49,
            "point_category" => "broker_agent",
            "transaction_type" => "high_bidder_lock_amount",
            'deduction_point' => '200',
        ]);

        // Point system for broker/sales agent
        // Let resever id until 50 for dealer
        DB::table('user_point_setting')->insert([
            'id' => 60,
            "point_category" => "dealer",
            "transaction_type" => "classified_listing_price",
            'deduction_point' => '0',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 61,
            "point_category" => "dealer",
            "transaction_type" => "auction_listing_price",
            'deduction_point' => '0',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 62,
            "point_category" => "dealer",
            "transaction_type" => "edit_fee",
            'deduction_point' => '5',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 63,
            "point_category" => "dealer",
            "transaction_type" => "switch_classified_to_auction",
            'deduction_point' => '10',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 64,
            "point_category" => "dealer",
            "transaction_type" => "switch_auction_to_classified",
            'deduction_point' => '10',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 65,
            "point_category" => "dealer",
            "transaction_type" => "withdraw_fee_reserve_not_meet",
            'deduction_point' => '20',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 66,
            "point_category" => "dealer",
            "transaction_type" => "withdraw_fee_reserve_meet",
            'deduction_point' => '200',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 67,
            "point_category" => "dealer",
            "transaction_type" => "bump_refresh",
            'deduction_point' => '5',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 68,
            "point_category" => "dealer",
            "transaction_type" => "bump_refresh_7_days",
            'deduction_point' => '20',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 69,
            "point_category" => "dealer",
            "transaction_type" => "feature_7_days",
            'deduction_point' => '30',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 70,
            "point_category" => "dealer",
            "transaction_type" => "feature_14_days",
            'deduction_point' => '50',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 71,
            "point_category" => "dealer",
            "transaction_type" => "highlight_ads",
            'deduction_point' => '15',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 72,
            "point_category" => "dealer",
            "transaction_type" => "relist_ad",
            'deduction_point' => '5',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 73,
            "point_category" => "dealer",
            "transaction_type" => "auction_winner_success_fee",
            'deduction_point' => '200',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 74,
            "point_category" => "dealer",
            "transaction_type" => "buy_decline_the_bid",
            'deduction_point' => '200',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 75,
            "point_category" => "dealer",
            "transaction_type" => "check_seller_info",
            'deduction_point' => '10',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 76,
            "point_category" => "dealer",
            "transaction_type" => "check_buyer_info_by_seller",
            'deduction_point' => '5',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 77,
            "point_category" => "dealer",
            "transaction_type" => "success_fee",
            'deduction_point' => '200',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 78,
            "point_category" => "dealer",
            "transaction_type" => "new_auction_listing_lock_amount",
            'deduction_point' => '500',
        ]);

        DB::table('user_point_setting')->insert([
            'id' => 79,
            "point_category" => "dealer",
            "transaction_type" => "high_bidder_lock_amount",
            'deduction_point' => '200',
        ]);

    }
}
