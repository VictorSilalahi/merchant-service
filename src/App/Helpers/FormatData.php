<?php

namespace App\Helpers;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\DataArraySerializer;

/**
 * Format response.
 */
class FormatData
{
    
    public static function Collection($model,$transformer)
    {
        $fractal = new Manager();
        $fractal->setSerializer(new DataArraySerializer);
        $resource = new Collection($model, $transformer);
        return $transactionData = $fractal->createData($resource)->toArray();
    }

    public static function Item($model,$transformer)
    {
        $fractal = new Manager();
        $fractal->setSerializer(new DataArraySerializer);
        $resource = new Item($model, $transformer);
        return $transactionData = $fractal->createData($resource)->toArray();
    }

    // Nominal
    public static function Nominal($number)
    {
        if($number == 0) {  
            $type = "";
            $short = 0;
        } elseif($number <= 999) {
            $short = $number;
        } elseif($number < 1000000) {
            $type = "K";
            $short = round($number/1000, 0,PHP_ROUND_HALF_UP);
        } elseif($number < 1000000000) {
            $type = "M";
            $short =  round($number/1000000, 0,PHP_ROUND_HALF_UP);
        } elseif($number >= 1000000000) {
            $type = "B";
            $short = round($number/1000000000,0,PHP_ROUND_HALF_UP);
        }
    
       return number_format($short,0)." $type";
    }
}
