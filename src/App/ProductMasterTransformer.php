<?php

namespace App;

use App\ProductMaster;
use League\Fractal;

class ProductMasterTransformer extends Fractal\TransformerAbstract
{
    private $_middleUrl = "";
    
    public function setMiddleUrl($url) {
        $this->_middleUrl = $url;
    }

    public function transform(ProductMaster $productMaster)
    {
        return [
            "id" => (string) $productMaster->guid,
            "name" => (string) $productMaster->product_name,
            "descriptions" => (string) $productMaster->description,
            "images" =>(string) $productMaster->images,
            "featured" =>(bool) $productMaster->featured,
            "featured_image" =>(string) $productMaster->featured_image,
            "category" => [
                'id' => $productMaster->category->guid,
                'name' => $productMaster->category->category_name,
                'descriptions' => $productMaster->category->description,
                'parent' => $productMaster->category->parent_id,
                'order_number' => $productMaster->category->parent_id,
            ],
            "created_at" => (string) $productMaster->created_at->format("Y-m-d H:i:s"),
            "updated_at" => (string) $productMaster->updated_at->format("Y-m-d H:i:s"),
            "created_by" => (string) $productMaster->updated_by,
            "order_number" => (integer) 0,
            "product_code" => (string) $productMaster->product_code,
            "discount" => (integer) 0,
            "free_delivery" => (integer) 1,
            "stock" => (integer) $productMaster->stock,
            "tags" => (string) $productMaster->tags,
            "unit" => "Pcs",
            "minimum_order" => 1,
            "price" => (float) $productMaster->price,
            "service_charge" => 0,
            "published" => (bool) $productMaster->published,
            "unit_sold" => null,
            "weight" => (float) $productMaster->weight,
            "price_discount" => 0


            // "category_id" => (string) $productMaster->category->guid,
            // "category_name" => (string) $productMaster->category->category_name,
            // "guid" => (string) $productMaster->guid,
            // "product_code" => (string) $productMaster->product_code,
            // "product_name" => (string) $productMaster->product_name,
            // "description" => (string) $productMaster->description,
            // "images" => (string) $productMaster->images,
            // "dimensions" => (string) $productMaster->dimensions,
            // "price" => (float) $productMaster->price,
            // "special_price" => $productMaster->special_price,
            // "discount_type" => (integer) $productMaster->discount_type,
            // "weight" => (float) $productMaster->weight,
            // "measurement_unit" => (string) $productMaster->measurement_unit,
            // "stock" => (integer) $productMaster->stock,
            // "adult_only" => (bool) $productMaster->adult_only,
            // "published" => (bool) $productMaster->published,
            // "status" => (integer) $productMaster->status,
            // "tags" => (string) $productMaster->tags,
            // "meta_data" => (string) $productMaster->meta_data,
            // "updated_by" => (string) $productMaster->updated_by,
            // "created_at" => (string) $productMaster->created_at->format("Y-m-d H:i:s"),
            // "updated_at" => (string) $productMaster->created_at->format("Y-m-d H:i:s"),
            // "links" => [
            //     "self" => $this->_middleUrl . "/product-master/{$productMaster->guid}",
            //     "category" => $this->_middleUrl . "/category/{$productMaster->category->guid}"
            // ]
        ];
    }
}
