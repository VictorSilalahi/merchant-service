<?php

namespace App;

use App\Balance;
use App\Merchant;
use League\Fractal;

class ReportTransformer extends Fractal\TransformerAbstract
{


    public function transform(Balance $balance)
    {
        return [
            "merchant_id" => (string) $balance->merchant_id,
            "income" => (int)$balance->total_masuk,
            "expense" => (int)$balance->total_keluar,
            "bulan" => date('F',strtotime($balance->bulan)),
            "tahun" => (string) date('Y',strtotime($balance->tahun)),
        ];
    }
}
