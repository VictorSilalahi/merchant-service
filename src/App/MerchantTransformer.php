<?php

namespace App;

use App\Merchant;
use League\Fractal;

class MerchantTransformer extends Fractal\TransformerAbstract
{


    public function transform(Merchant $product)
    {
        return [
            "id" => (string) $product->guid,
            "merchant_id" => (string) $product->merchant_id,
            "service_fee" => (int) $product->service_fee,
            "account_name" => (string) $product->account_name,
            "account_number" => (string) $product->account_number,
            "current_balance" => (int) $product->current_balance,
            "created_at" => (string) $product->created_at->format('Y-m-d H:i:s'),
            "updated_at" => (string) $product->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
