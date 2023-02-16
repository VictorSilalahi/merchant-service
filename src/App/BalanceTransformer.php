<?php

namespace App;

use App\Balance;
use App\Merchant;
use League\Fractal;

class BalanceTransformer extends Fractal\TransformerAbstract
{


    public function transform(Balance $balance)
    {
        return [
            "id" => (string) $balance->guid,
            "merchant_id" => (string) $balance->merchant_id,
            "nominal" => (int)$balance->nominal,
            "service_fee" => (int)$balance->service_fee,
            "result" => (int)$balance->result,
            "type" =>(string) $balance->type,
            "created_at" => (string) $balance->created_at->format('Y-m-d H:i:s'),
            "updated_at" => (string) $balance->updated_at->format('Y-m-d H:i:s'),
            "links" => [
                [
                    "rel" => "self",
                    "uri" => "/balance/detail/" . $balance->guid,
                ],
            ],
        ];
    }
}
