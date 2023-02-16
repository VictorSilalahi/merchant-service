<?php

namespace App;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;
use Spot\EventEmitter;

use Tuupola\Base62;
use Psr\Log\LogLevel;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;



class Outlet extends \Spot\Entity
{
    protected static $table = "outlet";

    public static function fields()
    {
        return [
            "id" => ["type" => "integer", "unsigned" => true, "primary" => true, "autoincrement" => true],
            "market_id" => ["type" => "integer", "unsigned" => true, "required" => true],
            "category_id" => ["type" => "integer", "unsigned" => true, "required" => true],
            "guid" => ["type" => "guid", "required" => true, "unique" => true],
            "outlet_name" => ["type" => "string", "length" => 255, "required" => true],
            "phone" => ["type" => "string", "length" => 255, "required" => true],
            "email" => ["type" => "string", "length" => 255],
            "address" => ["type" => "string", "length" => 255, "required" => true],
            "postal_code" => ["type" => "string", "length" => 255],
            "city" => ["type" => "string", "length" => 255],
            "lat" => ["type" => "float", "value" => 0],
            "long" => ["type" => "float", "value" => 0],
            "star" => ["type" => "integer", "value" => 0],
            "status" => ["type" => "smallint", "required" => true],
            "owner" => ["type" => "guid", "unique" => true, "required" => true],
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
            'outletcategory' => $mapper->belongsTo($entity, 'App\OutletCategory', 'category_id'),
            'products' => $mapper->hasMany($entity, 'App\Product', 'outlet_id'),
            'employees' => $mapper->hasMany($entity, 'App\Employee', 'outlet_id'),
            'market' => $mapper->belongsTo($entity, 'App\Market', 'market_id'),
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
            "outlet_id" => null,
            "outlet_category_id"=> null,
            "market_id" => null,
            "guid" => null,
            "contact_person" => null,
            "email" => null,
            "address" => null,
            "postal_code" => null,
            "city" => null,
            "lat" => null,
            "long" => null,
            "status" => null,
            "owner" => null,
            "updated_by" => null,
            "updated_at"   => null
        ]);
    }
}
