<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Feeds;

class FeedController extends ApiController
{
    public function getArticleNewsFeed()
    {
        //other working feed for future use
        // https://feeds.highgearmedia.com/?sites=GreenCarReports&tags=news
        // https://www.kbb.com/feed/

        $feed = Feeds::make('https://www.autoblog.com/rss.xml', 10);

        $data = array();
        $i = 0;
        foreach ($feed->get_items() as $item) {
            if ($enclosure = $item->get_enclosure()){
                $imageLink = $enclosure->get_link();
            } else {
                $imageLink = '';
            }
            $generatedData = array(
                'title'=>$item->get_title(),
                'description'=>$item->get_description(),
                'image' => $imageLink,
                'link' => $item->get_link()
            );

            array_push($data, $generatedData);

            if ( ++$i > 10 ) {break;}
        }


        return $this->successResponse($data);

    }

    public function getNewCarFeed()
    {
        // working https://www.motor1.com/rss/category/car-reviews
        // Working low image quality: https://www.goauto.com.au/rss/car-reviews/1.xml
        $feed = Feeds::make('https://www.motor1.com/rss/category/car-reviews/', 10);
        $data = array();
        $i = 0;
        foreach ($feed->get_items() as $item) {
           // dd($item);
            if ($enclosure = $item->get_enclosure()){
                $imageLink = $enclosure->get_link();
            } else {
                $imageLink = '';
            }
            $generatedData = array(
                'title'=>$item->get_title(),
                'description'=>$item->get_description(),
                'image' => $imageLink,
                'link' => $item->get_link()
            );

            array_push($data, $generatedData);

            if ( ++$i > 10 ) {break;}
        }


        return $this->successResponse($data);
    }
}
