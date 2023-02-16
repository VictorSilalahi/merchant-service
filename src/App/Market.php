<?php

namespace App;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;
use Spot\EventEmitter;

use Tuupola\Base62;
use Psr\Log\LogLevel;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;



class Market extends \Spot\Entity
{
    protected static $table = "market";

    public static function fields()
    {
        return [
            "id" => ["type" => "integer", "unsigned" => true, "primary" => true, "autoincrement" => true],
            "parent_id" => ["type" => "integer", "unsigned" => true, "value" => 0],
            "guid" => ["type" => "guid", "required" => true, "unique" => true],
            "market_name" => ["type" => "string", "length" => 255, "required" => true],
            "area_code" => ["type" => "string", "length" => 10],
            "address" => ["type" => "string", "length" => 255],
            "phone" => ["type" => "string", "length" => 255],
            "email" => ["type" => "string", "length" => 255],
            "pic" => ["type" => "string", "length" => 255],
            "images" => ["type" => "text"],
            "logo" => ["type" => "text"],
            "description" => ["type" => "text"],
            "lat" => ["type" => "float", "value" => "0"],
            "long" => ["type" => "float", "value" => "0"],
            "max_amount" => ["type" => "float", "value" => "0"],
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
            'productMarket' => $mapper->hasMany($entity, 'App\ProductMarket', 'market_id'),
            'adjustment' => $mapper->hasMany($entity, 'App\Adjustment', 'market_id'),
            'parentMarket' => $mapper->belongsTo($entity, 'App\Market', 'parent_id'),
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
            "parent_id" => 0,
            "market_name" => null,
            "address" => null,
            "phone" => null,
            "email" => null,
            "pic" => null,
            "description" => null,
            "images" => null,
            "logo" => null,
            "lat" => null,
            "long" => null,
            "status" => null,
            "updated_by" => null
        ]);
    }
}
