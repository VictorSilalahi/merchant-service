<?php

namespace App;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;
use Spot\EventEmitter;

use Tuupola\Base62;
use Psr\Log\LogLevel;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;



class OutletCategory extends \Spot\Entity
{
    protected static $table = "outlet_category";

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
            
            'outlets' => $mapper->hasMany($entity, 'App\Outlet', 'category_id'),
            'subCategories' => $mapper->hasMany($entity, 'App\OutletCategory', 'parent_id'),
            'parentCategory' => $mapper->belongsTo($entity, 'App\OutletCategory', 'parent_id'),
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
