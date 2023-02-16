<?php


use App\Balance;
use App\Merchant;
use App\BalanceTransformer;
use App\Helpers\FormatData;
use App\MerchantTransformer;
use App\Helpers\FormatResponse;
use App\Helpers\FunctionHelper;


/**
 * Add Balance IN
 */

$app->post("/balance/pay-in", function ($request, $response, $arguments) {

    $data = $request->getParsedBody();

    //Get merchant By id
    $merchant = $this->spot->mapper("App\Merchant")->first([
        "merchant_id" => $data['merchant_id']
    ]);

    // Check merchant balance
    if(!$merchant){
        return FormatResponse::Item($response,"Merchant not found");
    }

    // Create balance
    $result = FunctionHelper::reducePriceByPercentage($data['nominal'], $merchant->service_fee);
    $balance = $this->spot->mapper("App\Balance")->create([
        "merchant_id" => $merchant->guid,
        "nominal" => $data['nominal'],
        "service_fee" => $merchant->service_fee,
        "result" => $result,
        "trx_id" => $data['trx_id'],
        "type" => "IN"
    ]);

    // Update merchant balance
    $merchant->current_balance = $merchant->current_balance + $balance->result;
    $this->spot->mapper("App\Merchant")->save($merchant);

    
    $balanceTrans = new BalanceTransformer();
    $balanceData = FormatData::Item($balance, $balanceTrans);

    return FormatResponse::Item($response,"Add balance IN success" ,$balanceData['data']);

});


/**
 * Add Balance OUT
 */

$app->post("/balance/pay-out", function ($request, $response, $arguments) {

    $data = $request->getParsedBody();

    //Get merchant By id
    try {
        $merchant = $this->spot->mapper("App\Merchant")->first([
            "merchant_id" => $data['merchant_id']
        ]);
    } catch (\Throwable $th) {
        return FormatResponse::Item($response,"Merchant not found");
    }

    // Check merchant balance
    if($merchant->current_balance < $data['nominal']){
        return FormatResponse::Item($response,"Merchant balance not enough");
    }

    // Create balance
    $result = FunctionHelper::reducePriceByPercentage($data['nominal'], $merchant->service_fee);
    $balance = $this->spot->mapper("App\Balance")->create([
        "merchant_id" => $data['merchant_id'],
        "nominal" => $data['nominal'],
        "service_fee" => $merchant->service_fee,
        "result" => $result,
        "type" => "OUT"
    ]);

    // Update merchant balance
    $merchant->current_balance = $merchant->current_balance - $balance->result;
    $this->spot->mapper("App\Merchant")->save($merchant);
    
    $balanceTrans = new BalanceTransformer();
    $balanceData = FormatData::Item($balance, $balanceTrans);

    return FormatResponse::Item($response,"Add balance OUT success" ,$balanceData['data']);

});

/**
 * Get Balance HIstory
 */

$app->post("/balance/history", function ($request, $response, $arguments) {

    $data = $request->getParsedBody();

    //Get merchant By id
    try {
        $merchant = $this->spot->mapper("App\Merchant")->first([
            "merchant_id" => $data['merchant_id']
        ]);

    } catch (\Throwable $th) {
        return FormatResponse::Item($response,"Merchant not found");
    }

    // Get balance history
    $balance = $this->spot->mapper("App\Balance")->where([
        "merchant_id" => $data['merchant_id'],
    ])->status($data['status']??null)->order(['created_at' => $data['order_by']]);

    $balanceTrans = new BalanceTransformer();
    $balanceData = FormatData::Collection($balance, $balanceTrans);

    return FormatResponse::Item($response,"Get balance history success" ,$balanceData['data']);

});

/**
 * Get balance detail
 */

$app->get("/balance/detail/{id}", function ($request, $response, $arguments) {

    //Get balance By id
    try {
        $balance = $this->spot->mapper("App\Balance")->first([
            "guid" => $arguments['id']
        ]);
    } catch (\Throwable $th) {
        return FormatResponse::Item($response,"Balance not found");
    }

    $balanceTrans = new BalanceTransformer();
    $balanceData = FormatData::Item($balance, $balanceTrans);

    return FormatResponse::Item($response,"Get balance detail success" ,$balanceData['data']);

});