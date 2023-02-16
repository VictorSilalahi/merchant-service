<?php

namespace App\Helpers;

class FunctionHelper
{
    
    public static function reducePriceByPercentage($price, $percentage) {
        return $price - ($price * ($percentage / 100));
    }
    
}
    