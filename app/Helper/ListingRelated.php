<?php

namespace App\Helper;
use App\Models\Listing\Listing;

class ListingRelated
{
    public static function getImageForListing($listing_id) {
        //Getting image src
        $listing_image = Listing::select('car_images')->where('id', $listing_id)->get();
        $allImage = json_decode($listing_image);
        $allImage = $allImage[0]->car_images;
        $image_src = '';
        if($allImage->front_view != null) {
            $image_src = $allImage->front_view;
        } else if ($allImage->interior_front != null) {
            $image_src = $allImage->interior_front;
        } else if ($allImage->dashboard != null) {
            $image_src = $allImage->dashboard;
        } else if ($allImage->back_right != null) {
            $image_src = $allImage->back_right;
        } else if ($allImage->front_left != null) {
            $image_src = $allImage->front_left;
        } else if ($allImage->front_view != null) {
            $image_src = $allImage->front_view;
        } else if ($allImage->front_right != null) {
            $image_src = $allImage->front_right;
        } else if ($allImage->left_side_view != null) {
            $image_src = $allImage->left_side_view;
        } else if ($allImage->right_side_view != null) {
            $image_src = $allImage->right_side_view;
        } else if ($allImage->gear != null) {
            $image_src = $allImage->gear;
        } else if ($allImage->engine != null) {
            $image_src = $allImage->engine;
        } else if ($allImage->back_view != null) {
            $image_src = $allImage->back_view;
        }

        return $image_src;
    }
}
