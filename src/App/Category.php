<?php

namespace App;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;
use Spot\EventEmitter;

use Tuupola\Base62;
use Psr\Log\LogLevel;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;



class Category extends \Spot\Entity
{
    protected static $table = "category";

    public static function fields()
    {
        return [
            "id" => ["type" => "integer", "unsigned" => true, "primary" => true, "autoincrement" => true],
            "parent_id" => ["type" => "integer", "unsigned" => true],
            "guid" => ["type" => "guid", "required" => true, "unique" => true],
            "category_name" => ["type" => "string", "length" => 255, "required" => true],
            "description" => ["type" => "text"],
            "images" => ["type" => "text"],
            "status" => ["type" => "smallint", "required" => true],
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
            'productMasters' => $mapper->hasMany($entity, 'App\ProductMaster', 'category_id'),
            'products' => $mapper->hasMany($entity, 'App\Product', 'category_id'),
            'subCategories' => $mapper->hasMany($entity, 'App\Category', 'parent_id'),
            'parentCategory' => $mapper->belongsTo($entity, 'App\Category', 'parent_id'),
            'productMarket' => $mapper->hasMany($entity, 'App\ProductMarket', 'category_id'),
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
            "guid" => null,
            "category_name" => null,
            "description" => null,
            "images" => null,
            "status" => 0,
            "updated_by" => null,
            "created_at"   => null,
            "updated_at"   => null
        ]);
    }
}