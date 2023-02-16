<?php

namespace App;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;
use Spot\EventEmitter;

use Tuupola\Base62;
use Psr\Log\LogLevel;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;



class ProductImages extends \Spot\Entity
{
    protected static $table = "product_images";

    public static function fields()
    {
        return [
            "id" => ["type" => "integer", "unsigned" => true, "primary" => true, "autoincrement" => true],
            "product_id" => ["type" => "integer", "unsigned" => true],
            "image_big" => ["type" => "text"],
            "image_medium" => ["type" => "text"],
            "image_small" => ["type" => "text"],
            "image_thumb" => ["type" => "text"],
            "image_blurred" => ["type" => "text"],
            "image_original" => ["type" => "text"],
            "exported" => ["type" => "boolean"],
            "featured" => ["type" => "integer", "unsigned" => true],
        ];
    }

    public function clear()
    {
        $this->data([
            "id" => null,
            "product_id" => null,
            "image_big" => null,
            "image_medium" => null,
            "image_small" => null,
            "image_thumb" => null,
            "image_blurred" => null,
            "image_original" => null,
            "featured" => null,
        ]);
    }
}
