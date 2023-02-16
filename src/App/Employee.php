<?php

namespace App;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;
use Spot\EventEmitter;

use Tuupola\Base62;
use Psr\Log\LogLevel;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;



class Employee extends \Spot\Entity
{
    protected static $table = "employee";

    public static function fields()
    {
        return [
            "id" => ["type" => "integer", "unsigned" => true, "primary" => true, "autoincrement" => true],
            "outlet_id" => ["type" => "integer", "unsigned" => true, "required" => true],
            "guid" => ["type" => "guid", "required" => true, "unique" => true],
            "employee_name" => ["type" => "string", "length" => 255, "required" => true],
            "phone" => ["type" => "string", "length" => 255, "required" => true],
            "email" => ["type" => "string", "length" => 255],
            "gender" => ["type" => "boolean", "value" => true],
            "employee_address" => ["type" => "string", "length" => 255, "required" => true],
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
            'outlet' => $mapper->belongsTo($entity, 'App\Outlet', 'outlet_id'),
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
            "guid" => null,
            "employee_name" => null,
            "phone" => null,
            "email" => null,
            "gender" => null,
            "employee_address" => null,
            "updated_by" => null,
            "created_at"   => null,
            "updated_at"   => null,
        ]);
    }
}
