<?php

namespace App;

use App\Outlet;
use League\Fractal;

class OutletTransformer extends Fractal\TransformerAbstract
{
    private $_middleUrl = "";
    
    public function setMiddleUrl($url) {
        $this->_middleUrl = $url;
    }

    public function transform(Outlet $outlet)
    {
        return [
            "market_id" => (string) $outlet->market->guid,
            // "id" => (string) $outlet->id,
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
            "links" => [
                "self" => $this->_middleUrl . "/outlet-detail/{$outlet->guid}",
                "market" => $this->_middleUrl . "/market/{$outlet->market->guid}"
            ]
        ];
    }
}
