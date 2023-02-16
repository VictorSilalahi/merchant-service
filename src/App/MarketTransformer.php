<?php

namespace App;

use App\Market;
use League\Fractal;

class MarketTransformer extends Fractal\TransformerAbstract
{
    private $_middleUrl = "";
    
    public function setMiddleUrl($url) {
        $this->_middleUrl = $url;
    }

    public function transform(Market $market)
    {
        // echo json_encode($market);die();
        $adjustments = [];
        if (is_array($market->adjustments) || is_object($market->adjustments))
        {

        foreach($market->adjustments as $a => $b) {
            $b->created_at = $b->created_at->format("Y-m-d H:i:s");
            $b->updated_at = $b->updated_at->format("Y-m-d H:i:s");
           
            $adjustments[] = $b;
        } 
    }
        return [
            "id" => (int) $market->id,
            "guid" => (string) $market->guid,
            // "parent_id" => (string) $market->parentMarket->guid,
            // "parent_market" => (string) $market->parentMarket->market_name,
            "market_name" => (string) $market->market_name,
            "address" => (string) $market->address,
            "phone" => (string) $market->phone,
            "email" => (string) $market->email,
            "pic" => (string) $market->pic,
            "images" => (string) $market->images,
            "logo" => (string) $market->logo,
            "description" => (string) $market->description,
            "images" => (string) $market->images,
            "lat" => (float) $market->lat,
            "long" => (float) $market->long,
            "max_amount" => (float) $market->max_amount,
            "status" => (float) $market->status,
            "updated_by" => $market->updated_by,
            "created_at"   => (string) $market->created_at->format("Y-m-d H:i:s"),
            "updated_at"   => (string) $market->updated_at->format("Y-m-d H:i:s"),
            "setting"   => $adjustments,
            "links" => [
                "self" => $this->_middleUrl . "/market/{$market->guid}",
            ]
        ];
    }
}
