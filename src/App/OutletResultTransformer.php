<?php

namespace App;

use App\Outlet;
use League\Fractal;

class OutletResultTransformer extends Fractal\TransformerAbstract
{
    private $_middleUrl = "";
    
    public function setMiddleUrl($url) {
        $this->_middleUrl = $url;
    }

    public function transform(Outlet $outlet)
    {
        $items = [];
        $images = "";
        foreach($outlet->products as $k => $v) {
            if(strlen($images)!=0){
                $images .= ",".$v->images;
            }else{
                $images .= $v->images;
            }
            
            $items[] = $v;
        } 

        
        return [
            "market_id" => (string) $outlet->market->guid,
            "images" => $images,
            "guid" => (string) $outlet->guid,
            "outlet_name" => (string) $outlet->outlet_name,
            "phone" => (string) $outlet->phone,
            "email" => (string) $outlet->email,
            "address" => (string) $outlet->address,
            "postal_code" => (string) $outlet->postal_code,
            "city" => (string) $outlet->city,
            "lat" => (string) $outlet->lat,
            "long" => (float) $outlet->long,
            "status" => (integer) $outlet->status,
            "star" => (integer) $outlet->star,
            "owner" => (string) $outlet->owner,
            "updated_by" => (string) $outlet->updated_by,
            "created_at" => (string) $outlet->created_at->format("Y-m-d H:i:s"),
            "updated_at" => (string) $outlet->created_at->format("Y-m-d H:i:s"),
            "products" => $items,
            "links" => [
                "self" => $this->_middleUrl . "/outlet-detail/{$outlet->guid}",
                "market" => $this->_middleUrl . "/market/{$outlet->market->guid}"
            ]
        ];
    }
}
