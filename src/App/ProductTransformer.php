<?php

namespace App;

use App\Product;
use League\Fractal;

class ProductTransformer extends Fractal\TransformerAbstract
{
    private $_middleUrl = "";
    
    public function setMiddleUrl($url) {
        $this->_middleUrl = $url;
    }

    public function transform(Product $product)
    {
        $facilities = [];
        if (is_array($product->facilities) || is_object($product->facilities))
        {

        foreach($product->facilities as $a => $b) {
            $b->created_at = $b->created_at->format("Y-m-d H:i:s");
            $b->updated_at = $b->updated_at->format("Y-m-d H:i:s");
           
            $facilities[] = $b;
            } 
        }
        $rooms = [];
        if (is_array($product->rooms) || is_object($product->rooms))
        {

        foreach($product->rooms as $a => $b) {
            $b->created_at = $b->created_at->format("Y-m-d H:i:s");
            $b->updated_at = $b->updated_at->format("Y-m-d H:i:s");
           
            $rooms[] = $b;
            } 
        }

        return [
            "id" => (integer) $product->id,
            "guid" => (string) $product->guid,
            "name" => (string) $product->product_name,
            "descriptions" => (string) $product->description,
            "images" => (string) $product->images,
            "featured" =>(bool) $product->featured,
            "featured_image" =>(string) $product->featured_image,
            "category" => [
                'id' => $product->category->guid,
                'name' => $product->category->category_name,
                'descriptions' => $product->category->description,
                'parent' => $product->category->parent_id,
                'order_number' => $product->category->parent_id,
            ],
            "outlet" => [
                'id' => $product->outlet->guid,
                "outlet_name" => $product->outlet->outlet_name,
                "phone" =>  $product->outlet->phone,
                "email" =>  $product->outlet->email,
                "outlet_address" => $product->outlet->outlet_address,
            ],
            "created_at" =>(string) $product->created_at->format("Y-m-d H:i:s"),
            "updated_at" => (string) $product->updated_at->format("Y-m-d H:i:s"),
            "created_by" => (string) $product->updated_by,
            "product_code" => (string) $product->product_code,
            "stock" => (integer) $product->stock,
            "tags" => (string) $product->tags,
            "unit" => $product->measurement_unit,
            "price" => (float) $product->price,
            "published" => (bool) $product->published,
            "unit_sold" => null,
            "special_price" => (float) $product->special_price,
            "facilities" => $facilities,
            "rooms" => $rooms,
        ];
    }
}
