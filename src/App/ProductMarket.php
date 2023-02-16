<?php

namespace App;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;
use Spot\EventEmitter;

use Tuupola\Base62;
use Psr\Log\LogLevel;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;



class ProductMarket extends \Spot\Entity
{
    protected static $table = "product_market";

    public static function fields()
    {
        return [
            "id" => ["type" => "integer", "unsigned" => true, "primary" => true, "autoincrement" => true],
            "guid" => ["type" => "guid", "required" => true, "unique" => true],
            "market_id" => ["type" => "integer", "unsigned" => true, "required" => true],
            "product_id" => ["type" => "integer", "unsigned" => true, "required" => true],
            "category_id" => ["type" => "integer", "unsigned" => true, "required" => true],
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
            'market' => $mapper->belongsTo($entity, 'App\Market', 'market_id'),
            'productMasters' => $mapper->belongsTo($entity, 'App\ProductMaster', 'product_id'),
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
            "market_id" => null,
            "product_id" => null,
            "category_id" => null,
            "updated_by" => null,
            "created_at"   => null,
            "updated_at"   => null,
        ]);
    }
}
