<?php

namespace App;

use App\Cart;
use App\CartItems;
use League\Fractal;

class CartTransformer extends Fractal\TransformerAbstract
{
    private $_middleUrl = "";
    
    public function setMiddleUrl($url) {
        $this->_middleUrl = $url;
    }

    public function transform(Cart $cart)
    {
        // echo "cart\n";
        // echo print_r($cart->items[0]->products[0],true);die();
        // $cart->

        $items = [];
        $sub_total = 0;
        $adj = 0;
        $grand_total = 0;
        foreach($cart->items as $k => $v) {
            $v->product_name=$v->products->product_name;
            $v->product_image=$v->products->images;
            $v->created_at = $v->created_at->format("Y-m-d H:i:s");
            $v->updated_at = $v->updated_at->format("Y-m-d H:i:s");
            // $nightCount = $v->check_out-$v->check_in;
            $date1 = $v->check_in;
            $date2 = $v->check_out;
            $date1->setTime(0, 0, 0);
            $date2->setTime(0, 0, 0);
            $interval = $date2->diff($date1);
            $sub_total += $v->qty * $v->price * $interval->days;
            $date1->setTime(14,0,0);
            $date2->setTime(12, 0, 0);
            $items[] = $v;
        } 

        $adjustments = [];
        if (is_array($cart->adjustments) || is_object($cart->adjustments))
        {

        foreach($cart->adjustments as $a => $b) {
            // $v->product_name=$v->products->product_name;
            // $v->product_image=$v->products->images;
            $b->created_at = $b->created_at->format("Y-m-d H:i:s");
            $b->updated_at = $b->updated_at->format("Y-m-d H:i:s");
            if($b->value_type=="percentage"){
            $b->adjustment_nominal = ($b->adjustment_value/100)*$sub_total;
            }
            else{
            $b->adjustment_nominal = $b->adjustment_value;
            }
            $adj += $b->adjustment_nominal;
            $adjustments[] = $b;
        } 
    }
        $grand_total = $sub_total+$adj;


        return [
            "cart_id" => (string) $cart->id,
            "user_id" => (string) $cart->user_id,
            "outlet_id" => (string) $cart->outlet_id,
            "updated_by" => (string) $cart->updated_by,
            "created_at"   => (string) $cart->created_at->format("Y-m-d H:i:s"),
            "updated_at"   => (string) $cart->updated_at->format("Y-m-d H:i:s"),
            "sub_total" => (double) $sub_total,
            "grand_total" => (double) $grand_total,
            "adjustments" => $adjustments,
            "items" => $items,
            "links" => [
                "self" => $this->_middleUrl . "/cart/{$cart->cart_id}",
            ]
        ];
    }
}
