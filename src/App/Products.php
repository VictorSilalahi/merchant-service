<?php

namespace App;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;
use Spot\EventEmitter;

use Tuupola\Base62;
use Psr\Log\LogLevel;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;



class Products extends \Spot\Entity
{
    protected static $table = "products";

    public static function fields()
    {
        return [
            "id" => ["type" => "integer", "unsigned" => true, "primary" => true, "autoincrement" => true],
            "name" => ["type" => "string", "length" => 255, "required" => true],
            "descriptions" => ["type" => "text"],
            "category" => ["type" => "integer", "unsigned" => true],
            "created_timestamp" => ["type" => "integer", "unsigned" => true],
            "last_edited_timestamp" => ["type" => "integer", "unsigned" => true],
            "last_edited_by" => ["type" => "integer", "unsigned" => true],
            "created_by" => ["type" => "integer", "unsigned" => true],
            "order_number" => ["type" => "integer", "unsigned" => true],
            "product_code" => ["type" => "text"],
            "discount" => ["type" => "text"],
            "free_delivery" => ["type" => "integer", "unsigned" => true],
            "stock" => ["type" => "integer", "unsigned" => true],
            "tags" => ["type" => "text"],
            "featured_image" => ["type" => "integer", "unsigned" => true],
            "unit" => ["type" => "text"],
            "minimum_order" => ["type" => "integer", "unsigned" => true],
            "price" => ["type" => "integer", "unsigned" => true],
            "service_charge" => ["type" => "integer", "unsigned" => true],
            "published" => ["type" => "integer", "unsigned" => true],
            "featured" => ["type" => "integer", "unsigned" => true],
            "unit_sold" => ["type" => "integer", "unsigned" => true],
            "weight" => ["type" => "integer", "unsigned" => true],
            "is_need_reservation" => ["type" => "integer", "unsigned" => true],
        ];
    }

    public function clear()
    {
        $this->data([
            "id" => null,
            "name" => null,
            "descriptions" => null,
            "category" => null,
            "created_timestamp" => null,
            "last_edited_timestamp" => null,
            "last_edited_by" => null,
            "created_by" => null,
            "order_number" => null,
            "product_code" => null,
            "discount" => null,
            "free_delivery" => null,
            "stock" => null,
            "tags" => null,
            "featured_image" => null,
            "unit" => null,
            "minimum_order" => null,
            "price" => null,
            "service_charge" => null,
            "published" => null,
            "featured" => null,
            "unit_sold" => null,
            "weight" => null,
            "is_need_reservation" => null,
        ]);
    }
}
