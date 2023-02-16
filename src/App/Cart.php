<?php

namespace App;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;
use Spot\EventEmitter;

use Tuupola\Base62;
use Psr\Log\LogLevel;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;



class Cart extends \Spot\Entity
{
    protected static $table = "cart";

    public static function fields()
    {
        return [
            "id" => ["type" => "guid", "unsigned" => true, "primary" => true, "required" => true, "unique" => true],
            "user_id" => ["type" => "guid", "unsigned" => true],
            "outlet_id" => ["type" => "guid", "unsigned" => true],
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
            $entity->id = $uuid;

        });

        $emitter->on("beforeUpdate", function (\Spot\EntityInterface $entity, \Spot\MapperInterface $mapper) {
            $entity->updated_at = new \DateTime();
        });
    }

    public static function relations(Mapper $mapper, Entity $entity)
    {
        return [
            
            'items' => $mapper->hasMany($entity, 'App\CartItems', 'cart_id')
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
            "cart_id" => null,
            "user_id" => null,
            "updated_by" => null,
            "created_at"   => null,
            "updated_at"   => null
        ]);
    }
}
