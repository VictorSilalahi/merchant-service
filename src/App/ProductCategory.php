<?php

namespace App;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;
use Spot\EventEmitter;

use Tuupola\Base62;
use Psr\Log\LogLevel;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;



class ProductCategory extends \Spot\Entity
{
    protected static $table = "product_category";

    public static function fields()
    {
        return [
            "id" => ["type" => "integer", "unsigned" => true, "primary" => true, "autoincrement" => true],
            "name" => ["type" => "string", "length" => 255, "required" => true],
            "descriptions" => ["type" => "text"],
            "parent" => ["type" => "integer", "unsigned" => true],
            "order_number" => ["type" => "integer", "unsigned" => true]
        ];
    }

    public function clear()
    {
        $this->data([
            "id" => null,
            "name" => null,
            "descriptions" => null,
            "parent" => null,
            "order_number" => null
        ]);
    }
}
