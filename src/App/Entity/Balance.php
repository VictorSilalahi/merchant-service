<?php
namespace App\Entity;
use Spot\Mapper;

class Balance extends Mapper
{
    public function scopes()
    {
        return [
            'status' => function ($query,$status=null) {
                if ($status == 'IN') {
                    return $query->where(['type' => 'IN']);
                } else if ($status == 'OUT') {
                    return $query->where(['type' => 'OUT']);
                } else {
                    return $query;
                }
            },
            'periode' => function ($query,$period=null) {
                if ($period == 'today') {
                    return $query->where(['created_at >=' => date('Y-m-d 00:00:00')]);
                } else if ($period == 'week') {
                    return $query->where(['created_at >=' => date('Y-m-d 00:00:00',strtotime("last monday"))]);
                } else if ($period == 'month') {
                    return $query->where(['created_at >=' => date('Y-m-01 00:00:00')]);
                } else if ($period == 'year') {
                    return $query->where(['created_at >=' => date('Y-01-01 00:00:00')]);
                } else {
                    return $query;
                }
            },
        ];
    }

}