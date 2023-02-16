<?php

namespace App;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;
use Spot\EventEmitter;

use Tuupola\Base62;
use Psr\Log\LogLevel;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;



class CartItems extends \Spot\Entity
{
    protected static $table = "cartitems";

    public static function fields()
    {
        return [
            "cart_items_id" => ["type" => "guid", "unsigned" => true, "primary" => true, "required" => true, "unique" => true],
            "cart_id" => ["type" => "guid", "unsigned" => true],
            "product_idx" => ["type" => "integer", "unsigned" => true],
            "product_id" => ["type" => "guid", "unsigned" => true],
            "price" => ["type" => "decimal", "unsigned" => true],
            "qty" => ["type" => "integer", "unsigned" => true],
            "note" => ["type" => "text"],
            "check_in"   => ["type" => "datetime", "value" => new \DateTime(), "required" => true],
            "check_out"   => ["type" => "datetime", "value" => new \DateTime(), "required" => true],
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
            $entity->cart_items_id = $uuid;

        });

        $emitter->on("beforeUpdate", function (\Spot\EntityInterface $entity, \Spot\MapperInterface $mapper) {
            $entity->updated_at = new \DateTime();
        });
    }

    public static function relations(Mapper $mapper, Entity $entity)
    {
        return [
            
            'cart' => $mapper->belongsTo($entity, 'App\Cart', 'cart_id'),
            'products' => $mapper->belongsTo($entity, 'App\Product', 'product_idx'),
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
            "product_id" => null,
            "product_idx" => null,
            "price" => null,
            "qty" => null,
            "updated_by" => null,
            "check_in"   => null,
            "check_out"   => null,
            "created_at"   => null,
            "updated_at"   => null
        ]);
    }
}
