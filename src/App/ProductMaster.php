<?php

namespace App;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;
use Spot\EventEmitter;

use Tuupola\Base62;
use Psr\Log\LogLevel;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;



class ProductMaster extends \Spot\Entity
{
    protected static $table = "product_master";

    public static function fields()
    {
        return [
            "id" => ["type" => "integer", "unsigned" => true, "primary" => true, "autoincrement" => true],
            "category_id" => ["type" => "integer", "unsigned" => true, "required" => true],
            "guid" => ["type" => "guid", "required" => true, "unique" => true],
            "product_code" => ["type" => "string", "length" => 255, "required" => true],
            "product_name" => ["type" => "string", "length" => 255, "required" => true],
            "description" => ["type" => "text"],
            "images" => ["type" => "text"],
            "featured" => ["type" => "boolean", "value" => false],
            "featured_image" => ["type" => "string", "length" => 255],
            "dimensions" => ["type" => "string", "length" => 255],
            "price" => ["type" => "decimal", "required" => true, "value" => 0],
            "special_price" => ["type" => "decimal", "value" => 0],
            "discount_type" => ["type" => "smallint", "value" => 0],
            "weight" => ["type" => "float", "value" => 0],
            "measurement_unit" => ["type" => "string"],
            "stock" => ["type" => "integer", "value" => 0],
            "adult_only" => ["type" => "boolean", "value" => false],
            "published" => ["type" => "boolean", "value" => false],
            "status" => ["type" => "smallint", "required" => true],
            "tags" => ["type" => "string", "length" => 255],
            "meta_data" => ["type" => "text"],
            "updated_by" => ["type" => "guid", "required" => true],
            "created_at"   => ["type" => "datetime", "value" => new \DateTime(), "required" => true],
            "updated_at"   => ["type" => "datetime", "value" => new \DateTime(), "required" => true],
        ];
    }

    public static function events(EventEmitter $emitter)
    {
        $emitter->on("beforeInsert", function (\Spot\EntityInterface $entity, \Spot\MapperInterface $mapper) {
            // $entity->uid = (new Base62)->encode(random_bytes(9));
            $uuid = Uuid::uuid4();
            $entity->guid = $uuid;

        });

        $emitter->on("beforeUpdate", function (\Spot\EntityInterface $entity, \Spot\MapperInterface $mapper) {
            $entity->updated_at = new \DateTime();
        });
    }

    public static function relations(Mapper $mapper, Entity $entity)
    {
        return [
            'category' => $mapper->belongsTo($entity, 'App\Category', 'category_id'),
            'productMarket' => $mapper->hasMany($entity, 'App\ProductMarket', 'product_id')
        ];
    }

    public function timestamp()
    {
        return $this->updated_at->getTimestamp();
    }

    public function etag()
    {
        return md5($this->name . $this->timestamp());
    }

    public function clear()
    {
        $this->data([
            "category_id" => null,
            "product_code" => null,
            "product_name" => null,
            "description" => null,
            "images" => null,
            "featured" => null,
            "featured_image" => null,
            "dimensions" => null,
            "price" => null,
            "special_price" => null,
            "discount_type" => null,
            "weight" => null,
            "measurement_unit" => null,
            "stock" => null,
            "adult_only" => null,
            "published" => null,
            "status" => null,
            "tags" => null,
            "meta_data" => null,
            "updated_by" => null
        ]);
    }
}
