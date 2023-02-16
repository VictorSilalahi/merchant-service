<?php


use App\Balance;
use App\Merchant;
use App\ReportTransformer;
use App\BalanceTransformer;
use App\Helpers\FormatData;
use App\MerchantTransformer;
use App\Helpers\FormatResponse;
use App\Helpers\FunctionHelper;

/**
 * Report By Month
 */

$app->post("/report/this-month", function ($request, $response, $arguments) {

    $data = $request->getParsedBody();

    //Get merchant By id
    try {
        $merchant = $this->spot->mapper("App\Merchant")->first([
            "merchant_id" => $data['merchant_id']
        ]);
    } catch (\Throwable $th) {
        return FormatResponse::Item($response,"Merchant not found");
    }

    //Get Balance By merchant_id
    try {
        $balance = $this->spot->mapper("App\Balance")->where([
            "merchant_id" => $data['merchant_id']
        ]);
    } catch (\Throwable $th) {
        return FormatResponse::Item($response,"Balance not found");
    }

    $sql = "SELECT 
                SUM(CASE WHEN type = 'IN' THEN result ELSE 0 END) AS total_masuk, 
                SUM(CASE WHEN type = 'OUT' THEN result ELSE 0 END) AS total_keluar,  
                date_trunc('month', created_at) AS bulan, 
                date_trunc ('year', created_at) AS tahun,
                merchant_id
            FROM balance WHERE merchant_id = :merchant_id 
                AND date_trunc('month', created_at) = date_trunc('month', current_date) 
                AND date_part('year', created_at) = date_part('year', current_date)
            GROUP BY bulan, tahun, merchant_id";

    $balance = $this->spot->mapper("App\Balance")->query($sql,[
        "merchant_id" => $merchant->guid
    ])->first();

    $balanceTrans = new ReportTransformer();
    $balanceData = FormatData::Item($balance, $balanceTrans);

    return FormatResponse::Item($response,"Success get report by month" ,$balanceData['data']);


    
    
});

/**
 * Report By Year
 */

$app->post("/report/this-year", function ($request, $response, $arguments) {

    $data = $request->getParsedBody();

    //Get merchant By id
    try {
        $merchant = $this->spot->mapper("App\Merchant")->first([
            "merchant_id" => $data['merchant_id']
        ]);
    } catch (\Throwable $th) {
        return FormatResponse::Item($response,"Merchant not found");
    }

    //Get Balance By merchant_id
    try {
        $balance = $this->spot->mapper("App\Balance")->where([
            "merchant_id" => $data['merchant_id']
        ]);
    } catch (\Throwable $th) {
        return FormatResponse::Item($response,"Balance not found");
    }

    $sql = "SELECT 
                SUM(CASE WHEN type = 'IN' THEN result ELSE 0 END) AS total_masuk, 
                SUM(CASE WHEN type = 'OUT' THEN result ELSE 0 END) AS total_keluar,   
                date_trunc ('year', created_at) AS tahun,
                merchant_id
            FROM balance WHERE merchant_id = :merchant_id 
                AND date_part('year', created_at) = date_part('year', current_date)
            GROUP BY tahun, merchant_id";

    $balance = $this->spot->mapper("App\Balance")->query($sql,[
        "merchant_id" => $merchant->guid
    ])->first();

    $balanceTrans = new ReportTransformer();
    $balanceData = FormatData::Item($balance, $balanceTrans);

    return FormatResponse::Item($response,"Success get report by year" ,$balanceData['data']);

});

/**
 * Report By Week
 */

$app->post("/report/this-week", function ($request, $response, $arguments) {

    $data = $request->getParsedBody();

    //Get merchant By id
    try {
        $merchant = $this->spot->mapper("App\Merchant")->first([
            "merchant_id" => $data['merchant_id']
        ]);
    } catch (\Throwable $th) {
        return FormatResponse::Item($response,"Merchant not found");
    }

    //Get Balance By merchant_id
    try {
        $balance = $this->spot->mapper("App\Balance")->where([
            "merchant_id" => $data['merchant_id']
        ]);
    } catch (\Throwable $th) {
        return FormatResponse::Item($response,"Balance not found");
    }

    $sql = "SELECT 
                SUM(CASE WHEN type = 'IN' THEN result ELSE 0 END) AS total_masuk, 
                SUM(CASE WHEN type = 'OUT' THEN result ELSE 0 END) AS total_keluar,   
                date_trunc ('week', created_at) AS minggu,
                merchant_id
            FROM balance WHERE merchant_id = :merchant_id 
                AND date_trunc('week', created_at) = date_trunc('week', current_date) 
                AND date_part('year', created_at) = date_part('year', current_date)
            GROUP BY minggu, merchant_id";

    $balance = $this->spot->mapper("App\Balance")->query($sql,[
        "merchant_id" => $merchant->guid
    ])->first();

    $balanceTrans = new ReportTransformer();
    $balanceData = FormatData::Item($balance, $balanceTrans);

    return FormatResponse::Item($response,"Success get report by week" ,$balanceData['data']);

});

/**
 * Report By Today
 */

$app->post("/report/today", function ($request, $response, $arguments) {

    $data = $request->getParsedBody();

    //Get merchant By id
    try {
        $merchant = $this->spot->mapper("App\Merchant")->first([
            "merchant_id" => $data['merchant_id']
        ]);
    } catch (\Throwable $th) {
        return FormatResponse::Item($response,"Merchant not found");
    }

    //Get Balance By merchant_id
    try {
        $balance = $this->spot->mapper("App\Balance")->where([
            "merchant_id" => $data['merchant_id']
        ]);
    } catch (\Throwable $th) {
        return FormatResponse::Item($response,"Balance not found");
    }

    $sql = "SELECT 
                SUM(CASE WHEN type = 'IN' THEN result ELSE 0 END) AS total_masuk, 
                SUM(CASE WHEN type = 'OUT' THEN result ELSE 0 END) AS total_keluar,   
                date_trunc ('day', created_at) AS hari,
                merchant_id
            FROM balance WHERE merchant_id = :merchant_id 
                AND date_trunc('day', created_at) = date_trunc('day', current_date) 
                AND date_part('year', created_at) = date_part('year', current_date)
            GROUP BY hari, merchant_id";

    $balance = $this->spot->mapper("App\Balance")->query($sql,[
        "merchant_id" => $merchant->guid
    ])->first();

    $balanceTrans = new ReportTransformer();
    $balanceData = FormatData::Item($balance, $balanceTrans);

    return FormatResponse::Item($response,"Success get report by today" ,$balanceData['data']);

});