<?php


use App\Merchant;
use App\Helpers\FormatData;
use App\MerchantTransformer;
use App\Helpers\FormatResponse;


/**
 * Save setting merchant
 */

$app->post("/setting/save", function ($request, $response, $arguments) {

    $data = $request->getParsedBody();
    
    $merchant = new Merchant();
    $merchant->merchant_id = $data['merchant_id'];
    $merchant->service_fee = $data['service_fee'];
    $merchant->account_name = $data['account_name'];
    $merchant->account_number = $data['account_number'];
    $this->spot->mapper("App\Merchant")->save($merchant);
    
    $merchantTrans = new MerchantTransformer();
    $merchantData = FormatData::Item($merchant, $merchantTrans);

    return FormatResponse::Item($response,"Success save setting merchant" ,$merchantData['data']);
        
});

/**
 * Update setting merchant
 */

$app->post("/setting/update", function ($request, $response, $arguments) {

    $data = $request->getParsedBody();
    
    $merchant = $this->spot->mapper("App\Merchant")->first([
        "merchant_id" => $data['merchant_id']
    ]);
    
    $merchant->service_fee = $data['service_fee'];
    $merchant->account_name = $data['account_name'];
    $merchant->account_number = $data['account_number'];
    
    $this->spot->mapper("App\Merchant")->save($merchant);
    
    $merchantTrans = new MerchantTransformer();
    $merchantData = FormatData::Item($merchant, $merchantTrans);

    return FormatResponse::Item($response,"Success update setting merchant" ,$merchantData['data']);

});

/**
 * Get setting By Merchant Id
 */

$app->get("/setting/{merchant_id}", function ($request, $response, $arguments) {

    
    $merchant = $this->spot->mapper("App\Merchant")->first([
        "merchant_id" => $arguments['merchant_id']
    ]);

    if(!$merchant){
        return FormatResponse::Error($response, "Merchant not found");
    }
    
    $merchantTrans = new MerchantTransformer();
    $merchantData = FormatData::Item($merchant, $merchantTrans);

    return FormatResponse::Item($response,"Success get setting merchant" ,$merchantData['data']);

});

$app->get("/outlet/rooms", function ($request, $response, $arguments) {

    // memperlihatkan data semua product di dalam outlet
    $outlets = getOutlets($_SERVER["HTTP_AUTHORIZATION"]);

    // melihat outlet di product
    $pgSql = new PgSql();
    $ndb = $pgSql->create($_ENV["STAG_DBproduct"]);

    // periksa jumlah outlet
    $sql = "select * from public.product where outlet_id=".$outlets[0]->id." and category_id in (1,3,4,15)";
    $temp = $ndb->query($sql);

    $products = array();
    foreach ($temp as $row) {
        array_push($products, array("id" => $row["id"], "guid"=>$row["guid"], "product_code"=>$row["product_code"], "product_name"=>$row["product_name"], "price"=>$row["price"], "stocks"=>$row["stock"], "img"=>$row["images"]));
    }
    $ndb = NULL;

    $data["status"] = "ok";
    $data["stat-operation"] = "Rooms in Outlet";
    $data["outlet_id"] = $outlets[0]->id;
    $data["data"] = $products;
    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

});

$app->get("/outlet/fnb", function ($request, $response, $arguments) {

    // memperlihatkan data semua product di dalam outlet
    $outlets = getOutlets($_SERVER["HTTP_AUTHORIZATION"]);

    // melihat outlet di product
    $pgSql = new PgSql();
    $ndb = $pgSql->create($_ENV["STAG_DBproduct"]);

    // periksa jumlah outlet
    $sql = "select * from public.product where outlet_id=".$outlets[0]->id." and category_id not in (1,3,4,15,29,30)";
    $temp = $ndb->query($sql);

    $products = array();
    foreach ($temp as $row) {
        array_push($products, array("id" => $row["id"], "guid"=>$row["guid"], "product_code"=>$row["product_code"], "product_name"=>$row["product_name"], "price"=>$row["price"], "stocks"=>$row["stock"]));
    }
    $ndb = NULL;

    $data["status"] = "ok";
    $data["stat-operation"] = "FnB in Outlet";
    $data["outlet_id"] = $outlets[0]->id;
    $data["data"] = $products;
    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

});

$app->get("/outlet/spa", function ($request, $response, $arguments) {

    // memperlihatkan data semua product di dalam outlet
    $outlets = getOutlets($_SERVER["HTTP_AUTHORIZATION"]);

    // melihat outlet di product
    $pgSql = new PgSql();
    $ndb = $pgSql->create($_ENV["STAG_DBproduct"]);

    // periksa jumlah outlet
    $sql = "select * from public.product where outlet_id=".$outlets[0]->id." and category_id in (29,30)";
    $temp = $ndb->query($sql);

    $products = array();
    foreach ($temp as $row) {
        array_push($products, array("id" => $row["id"], "guid"=>$row["guid"], "product_code"=>$row["product_code"], "product_name"=>$row["product_name"], "price"=>$row["price"], "stocks"=>$row["stock"]));
    }
    $ndb = NULL;

    $data["status"] = "ok";
    $data["stat-operation"] = "Spa Product in Outlet";
    $data["outlet_id"] = $outlets[0]->id;
    $data["data"] = $products;
    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

});