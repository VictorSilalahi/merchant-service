<?php

namespace App;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;
use Spot\EventEmitter;

use Tuupola\Base62;
use Psr\Log\LogLevel;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;



class Merchant extends \Spot\Entity
{
    protected static $table = "merchant";

    public static function fields()
    {
        return [
            "guid" => ["type" => "guid", "unsigned" => true, "primary" => true],
            "merchant_id" => ["type" => "guid", "required" => false, "unique" => true],
            "service_fee" => ["type" => "decimal", "required" => false],
            "account_name" => ["type" => "string", "required" => false],
            "account_number" => ["type" => "string", "required" => false],
            "current_balance" => ["type" => "decimal", "required" => false],

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


    public function timestamp()
    {
        return $this->updated_at->getTimestamp();
    }

    public function etag()
    {
        return md5($this->name . $this->timestamp());
    }
}
