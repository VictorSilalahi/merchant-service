<?php

use Tuupola\Base62;
use Firebase\JWT\JWT;
use Ramsey\Uuid\Uuid;
use GuzzleHttp\Client;

use League\Fractal\Manager;
use League\Fractal\Serializer\DataArraySerializer;
use League\Fractal\Resource\Item;

use App\Cart;
use App\Outlet;
use App\Product;
use App\CartItems;
use App\CartTransformer;
use App\CartItemTransformer;

function getRefreshPayload($payload) {
    $future = new DateTime("now +7 day");

    $payload2 = [
        "iat" => $payload["iat"],
        "exp" => $future->getTimeStamp(),
        "jti" => $payload["jti"],
        "sub" => $payload["sub"],
        "isMerchant" => $payload["isMerchant"],
        "uid" => $payload["uid"],
        "outlets" => $payload["outlets"]
    ];

    return $payload2;
}

function formatCartData(Cart $cart)
{
    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $cartTransformer = new CartTransformer();
    $cartTransformer->setMiddleUrl(getenv("MIDDLE_URL"));
    $resource = new Item($cart, $cartTransformer);
    $cartData = $fractal->createData($resource)->toArray();
    return $cartData;
}

function formatCartItemData(CartItems $cart)
{
    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $cartTransformer = new CartItemTransformer();
    $cartTransformer->setMiddleUrl(getenv("MIDDLE_URL"));
    $resource = new Item($cart, $cartTransformer);
    $cartData = $fractal->createData($resource)->toArray();
    return $cartData;
}

function sendMail($email, $username) {

        //Create a new PHPMailer instance
        $mail = new PHPMailer;
        //Tell PHPMailer to use SMTP
        $mail->isSMTP();
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function($str, $level) {$this->logger->addInfo("debug level $level; message: $str");};
        //Set the hostname of the mail server
        $mail->Host = getenv("STAG_MAIL_SMTP");
        // use
        // $mail->Host = gethostbyname('smtp.gmail.com');
        // if your network does not support SMTP over IPv6
        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $mail->Port = getenv("STAG_MAIL_SMTP_PORT");
        //Set the encryption system to use - ssl (deprecated) or tls
        $mail->SMTPSecure = getenv("STAG_MAIL_SMTP_TYPE");
        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;
        //Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = getenv("STAG_MAIL_USER");
        //Password to use for SMTP authentication
        $mail->Password = getenv("STAG_MAIL_PASSWORD");
        //Set who the message is to be sent from
        $mail->setFrom(getenv("STAG_MAIL_USER"), getenv("STAG_MAIL_USER"));
        //Set an alternative reply-to address
        $mail->addReplyTo(getenv("STAG_MAIL_USER"), getenv("STAG_MAIL_USER"));
        //Set who the message is to be sent to
        $mail->addAddress($email, $username);
        //Set the subject line
        $mail->isHTML(true);
        $mail->Subject = 'Terima Kasih atas penggunaan fasilitas Hotel kami. Silahkan anda memperbaharui password anda disini.';
        $mail->Body = "Klik link dibawah ini untuk mengubah password anda <br/> \n<a href=\"".getenv("RECOVER_URL").$user->password_reset_token
                . "\">".getenv("RECOVER_URL").$user->password_reset_token."</a>";
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        // $mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
        // //Replace the plain text body with one created manually
        // $mail->AltBody = 'This is a plain-text message body';
        // //Attach an image file
        // $mail->addAttachment('images/phpmailer_mini.png');
    
        //send the message, check for errors
        if (!$mail->send()) {
            $this->logger->addInfo("Failed to send mail. E-mail address: $email\n");
    
            $data["status"] = "error";
            $data["message"] = "Something wrong about sending e-mail, please contact administrator!";
                           
            return $response->withStatus(500)
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        
            // echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            $this->logger->addInfo("Mail has been sent. E-mail address: $email\n");
    
            $data["status"] = "ok";
            $data["message"] = "Instruksi untuk merubah password telah dikirim ke email.";
                           
            return $response->withStatus(200)
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    

        }

}

$app->post("/user/login", function ($request, $response, $args) {

    // user login
    $body = $request->getParsedBody();
    
    $username = $body["username"];
    $password = $body["password"];

    $pgSql = new PgSql();
    $ndb = $pgSql->create($_ENV["STAG_DBuser"]);

    $sql = "select count(*) jumlah from public.user where username = '".$username."' and status=1";
    $temp = $ndb->query($sql);
    if (!$temp) {
        throw new Exception\ForbiddenException("Database connection error!", 403);
    }
    $jumlah = $temp->fetch(\PDO::FETCH_ASSOC);
    if ($jumlah["jumlah"]!=1) {
        throw new Exception\ForbiddenException("Your credential information is invalid.", 403);
    }

    $sql = "select password_hash ph from public.user where username = '".$username."' and status=1";
    $temp = $ndb->query($sql);
    if (!$temp) {
        throw new Exception\ForbiddenException("Database connection error!", 403);
    }
    $ph = $temp->fetch(\PDO::FETCH_ASSOC);

    $hasil = password_verify($password, $ph["ph"]);
    if ($hasil!==true) {
        throw new Exception\ForbiddenException("User not authorized!", 403);
    }

    // ambil uid
    $sql = "select * from public.user where username = '".$username."' and status=1";
    $temp = $ndb->query($sql);
    if (!$temp) {
        throw new Exception\ForbiddenException("Database connection error!", 403);
    }
    $user = $temp->fetch(\PDO::FETCH_ASSOC);

    // melihat outlet di product
    $pgSql = new PgSql();
    $ndb = $pgSql->create($_ENV["STAG_DBproduct"]);

    // periksa jumlah outlet
    $sql = "select count(*) jumlah from public.outlet where owner='".$user['uid']."'";
    $temp = $ndb->query($sql);
    $jumlah =  $temp->fetch(\PDO::FETCH_ASSOC);
    if ($jumlah["jumlah"]>0) {

        // sertakan data outlet
        $temp = array();
        $sql = "select outlet.id, outlet.guid, outlet.outlet_name, outlet.address, outlet_category.category_name from public.outlet, public.outlet_category where outlet.category_id=outlet_category.id and outlet.owner='".$user['uid']."'";
        $temp_outlets = $ndb->query($sql)->fetchAll();
        foreach ($temp_outlets as $row) {
            array_push($temp, array("id" => $row["id"], "guid"=>$row["guid"], "name"=>$row["outlet_name"], "address"=>$row["address"], "category"=>$row["category_name"]));
        }
        $ndb = NULL;

        $now = new DateTime();
        $future = new DateTime("now +2 day");
        $b62 = new Base62;
        $jti = $b62->encode(random_bytes(16));
        $payload = [
            "iat" => $now->getTimeStamp(),
            "exp" => $future->getTimeStamp(),
            "jti" => $jti,
            "sub" => $user['full_name'],
            "isMerchant" => true,
            "uid" => $user['uid'],
            "outlets" => json_encode($temp)
        ];

        $secret = $_ENV["STAG_JWT_SECRET"];
        $token = JWT::encode($payload, $secret, "HS256");
        $payload2 = getRefreshPayload($payload);
        $refreshToken = JWT::encode($payload2, $secret, "HS256");


        $data["status"] = "ok";
        $data["token"] = $token;
        $data["refresh_token"] = $refreshToken;
        $data["user"] = $user;
        $data["isMerchant"] = 1;
        $data["outlets"] = $temp;

        return $response->withStatus(201)
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

    } else {
        throw new Exception\ForbiddenException("User not authorized!", 403);       
    }

});

$app->post("/sale/new-cart", function ($request, $response, $args) 
{
    // fungsi ini memerlukan penambahan column outlet_id pada tabel user di merchant service

    $spot = makeSpot($_ENV['STAG_DBproduct']);
    $body = $request->getParsedBody();
    // $productRef = $this->spot->mapper("App\Product")->all()->where(["guid" => $body["product_id"]])->first();
    // $outletRef = $this->spot->mapper("App\Outlet")->all()->where(["id" => $productRef->outlet_id])->first();
    $productRef = $spot->mapper("App\Product")->all()->where(["guid" => $body["product_id"]])->first();
    $outletRef = $spot->mapper("App\Outlet")->all()->where(["id" => $productRef->outlet_id])->first();
    
    $check_in =  $body["check_in"];
    $check_out = $body["check_out"];
    $product_id = $body['product_id'];

    // echo json_encode($check_in); die();

    $check_in = $check_in . " 14:00:00";
    $check_out = $check_out . " 12:00:00";
    
    $client = new GuzzleHttp\Client();
    $res = $client->request('POST', $_ENV["STAG_MAIN_DOMAIN"] . '/trx-service/get-booked', [
        "headers" => [
            'Content-Type' => 'application/json'
        ],
        "body" => json_encode([
            "product_id" => $product_id,
            "check_in" => $check_in,
            "check_out" => $check_out,
            "DB_NAME"=> $_ENV['STAG_DBtrx']
        ])
    ]);

    
    $this->logger->info(' [x] Product ID :' . $product_id);
    $this->logger->info(' [x] Status Code :' . $res->getStatusCode());
    $this->logger->info(' [x] Body :' . $res->getBody()->getContents());
    $responsedata = $res->getBody()->__toString();
    $responsedatas = json_decode($responsedata);
    $booked = $responsedatas->booked;

    if($productRef->stock<=$booked){
        throw new ForbiddenException("Stock habis.", 403);
    }

    $check_in = DateTime::createFromFormat('Y-m-d H:i:s', $check_in);
    $check_out = DateTime::createFromFormat('Y-m-d H:i:s', $check_out);

    $uid = getUserId($_SERVER['HTTP_AUTHORIZATION']);

    // if (false === $keranjang = $this->spot->mapper("App\Cart")->all()->where(["user_id" => $uid])->first()) {
    if (false === $keranjang = $spot->mapper("App\Cart")->all()->where(["user_id" => $uid])->first()) {
            $cart = new Cart([
            "user_id" => $uid,
            "updated_by" => $uid,
            "outlet_id" => $outletRef->guid
        ]);

        // $idCart = $this->spot->mapper("App\Cart")->insert($cart);
        $idCart = $spot->mapper("App\Cart")->insert($cart);
    } 
    else 
    {
        if($keranjang->outlet_id != $outletRef->guid){
            // $this->spot->mapper("App\CartItems")->query("DELETE FROM cartitems WHERE cart_id = ?", [$keranjang->id]);
            // $this->spot->mapper("App\Cart")->query("DELETE FROM cart WHERE id = ?", [$keranjang->id]);
            $spot->mapper("App\CartItems")->query("DELETE FROM cartitems WHERE cart_id = ?", [$keranjang->id]);
            $spot->mapper("App\Cart")->query("DELETE FROM cart WHERE id = ?", [$keranjang->id]);
            $cart = new Cart([
                "user_id" => $uid,
                "updated_by" => $uid,
                "outlet_id" => $outletRef->guid
            ]);
    
            // $idCart = $this->spot->mapper("App\Cart")->insert($cart);
            $idCart = $spot->mapper("App\Cart")->insert($cart);
        }else{
            $idCart = $keranjang->id;
            $cart = $keranjang;
        }
        
    }


    // if (false === $product = $this->spot->mapper("App\CartItems")->all()->where(["product_id" => $body["product_id"], "cart_id" => $idCart])->first()) {
    // if (false === $product = $spot->mapper("App\CartItems")->all()->where(["product_id" => $body["product_id"], "cart_id" => $idCart])->first()) {
            // $products = $this->spot->mapper("App\Product")->all()->where(["guid"=> $items["product_id"]])->first();
        // echo json_encode($products); die();


        if($body["qty"] <= 0){
            $body["qty"] = 1;
        }
        $product = new CartItems([
            "cart_id" => $idCart,
            "product_id" => $body["product_id"],
            "product_idx" => $productRef->id,
            "price" => $productRef->price,
            "qty" => $body["qty"],
            "note" => $body["note"],
            "check_in" => $check_in,
            "check_out" => $check_out,
            "updated_by" => $uid

        ]);
        // $idCartItems = $this->spot->mapper("App\CartItems")->insert($product);
        $idCartItems = $spot->mapper("App\CartItems")->insert($product);

    // } else {
    //     // $idCartItems = $product->cart_items_id;
    //     // $product->cart_items_id =$idCartItems;
    //     $quantity = $product->qty;
    //     if($body["qty"] <= 0){
    //         $body["qty"] = 1;
    //     }
    //     // $product->clear();
    //     $check_in =  $body["check_in"];
    //     $check_out = $body["check_out"];

    //     // echo json_encode($check_in); die();

    //     $check_in = $check_in . " 14:00:00";
    //     $check_out = $check_out . " 12:00:00";
    //     $check_in = DateTime::createFromFormat('Y-m-d H:i:s', $check_in);
    //     $check_out = DateTime::createFromFormat('Y-m-d H:i:s', $check_out);

    //     $product->price = $productRef->price;
    //     $product->qty = $body["qty"];
    //     $product->note = $body["note"];
    //     $product->check_in = $check_in;
    //     $product->check_out = $check_out;
    //     // echo $product->cart_items_id;
    //     // echo $product;

    //     // $this->spot->mapper("App\CartItems")->save($product);
    //     $spot->mapper("App\CartItems")->save($product);
    // }





    // $adjustment = $this->spot->mapper("App\Adjustment")->all();
    $adjustment = $spot->mapper("App\Adjustment")->all();
    $cart->adjustments = $adjustment;

    // var_dump($productMaster); die();
    $cartData = formatCartData($cart);

    $data["status"] = "ok";
    $data["results"] = $cartData["data"];

    return $response->withStatus(201)
        ->withHeader("Content-Type", "application/json")
        ->withHeader("Location", $data["results"]["links"]["self"])
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

});



$app->get("/sale/mycart", function ($request, $response, $arguments) {

    /* Check if token has needed scope */
    // if (false === $this->token->hasScope(["category.list"])) {
    //     throw new ForbiddenException("Token not allowed to list categories.", 403);
    // }

    $uid = getUserId($_SERVER['HTTP_AUTHORIZATION']);

    $spot = makeSpot($_ENV["STAG_DBproduct"]);

    /* Use ETag and date from Product with most recent update. */
    // $first = $this->spot->mapper("App\Cart")
    //     ->all()
    //     ->where(["user_id" => $uid])->first();
    $first = $spot->mapper("App\Cart")
        ->all()
        ->where(["user_id" => $uid])->first();

    /* Add Last-Modified and ETag headers to response when atleast on media exists. */
    $cache = makeCache();

    if ($first) {
        // throw new ForbiddenException("ETag : " . $first->timestamp(), 403);
        // $response = $this->cache->withEtag($response, $first->etag());
        // $response = $this->cache->withLastModified($response, $first->timestamp());
        $response = $cache->withEtag($response, $first->etag());
        $response = $cache->withLastModified($response, $first->timestamp());
    }


    /* If-Modified-Since and If-None-Match request header handling. */
    /* Heads up! Apache removes previously set Last-Modified header */
    /* from 304 Not Modified responses. */
    // if ($this->cache->isNotModified($request, $response)) {
    //     return $response->withStatus(304);
    // }
    if ($cache->isNotModified($request, $response)) {
        return $response->withStatus(304);
    }

    // if (false === $cart = $this->spot->mapper("App\Cart")
    //     ->first(["user_id" => $uid])
    if (false === $cart = $spot->mapper("App\Cart")
        ->first(["user_id" => $uid])
    ) {
        $data["status"] = "ok";
        $data["results"] = null;
    } else {

        // echo json_encode($cart->items);die();

        /* Serialize the response data. */
        $fractal = new Manager();
        $fractal->setSerializer(new DataArraySerializer);
        $cartTransformer = new CartTransformer();
        $cartTransformer->setMiddleUrl(getenv("MIDDLE_URL"));
        $resource = new Item($cart, $cartTransformer);
        // $data = $fractal->createData($resource)->toArray();
        $adjustment = [];
        $cart->adjustments = $adjustment;
        $cartData = $fractal->createData($resource)->toArray();

        // $cartData = formatCartData($cart);
        $data["status"] = "ok";
        $data["results"] = $cartData["data"];
    }


    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->post("/sale/remove-cartitem/{ciid}", function ($request, $response, $arguments) {

    /* Check if token has needed scope */
    // if (false === $this->token->hasScope(["category.list"])) {
    //     throw new ForbiddenException("Token not allowed to list categories.", 403);
    // }

    /* Use ETag and date from Product with most recent update. */
    $spot = makeSpot($_ENV["STAG_DBproduct"]);

    // if (false === $item = $this->spot->mapper("App\CartItems")->first([
    //     "cart_items_id" => $arguments["ciid"]
    // ])) {
    //     throw new NotFoundException("Item not found.", 404);
    // };
    if (false === $item = $spot->mapper("App\CartItems")->first([
        "cart_items_id" => $arguments["ciid"]
    ])) {
        throw new NotFoundException("Item not found.", 404);
    };

    $id=$item->cart_id;
    // $countItem = $this->spot->mapper("App\CartItems")->all()->where(["cart_id"=>$item->cart_id]);
    $countItem = $spot->mapper("App\CartItems")->all()->where(["cart_id"=>$item->cart_id]);
    if(count($countItem)==1){
        // $this->spot->mapper("App\CartItems")->delete($item);
        // $this->spot->mapper("App\Cart")->query("DELETE FROM cart WHERE id = ?", [$id]);
        $spot->mapper("App\CartItems")->delete($item);
        $spot->mapper("App\Cart")->query("DELETE FROM cart WHERE id = ?", [$id]);
    }else{
        // $this->spot->mapper("App\CartItems")->delete($item);
        $spot->mapper("App\CartItems")->delete($item);
    }
    
    $data["status"] = "ok";
    $data["results"] = "Cart item has been deleted.";


    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});


$app->post("/sale/submit-cart", function ($request, $response, $arguments) {

    try {

        $uid = getUserId($_SERVER['HTTP_AUTHORIZATION']);
        
        $body = $request->getParsedBody();
        $cart_id = $body["cart"];
        $promo = $body["promo"];

        $spot = makeSpot($_ENV["STAG_DBproduct"]);

        /* Use ETag and date from Product with most recent update. */
        // $first = $this->spot->mapper("App\Cart")
        //     ->all()
        //     ->where(["user_id" => $uid, "id"=>$cart_id])->first();
        $first = $spot->mapper("App\Cart")
            ->all()
            ->where(["user_id" => $uid, "id"=>$cart_id])->first();

        $cache = makeCache();
         
        /* Add Last-Modified and ETag headers to response when atleast on media exists. */
        if ($first) {
            // throw new ForbiddenException("ETag : " . $first->timestamp(), 403);
            // $response = $this->cache->withEtag($response, $first->etag());
            // $response = $this->cache->withLastModified($response, $first->timestamp());
            $response = $cache->withEtag($response, $first->etag());
            $response = $cache->withLastModified($response, $first->timestamp());
        }


        /* If-Modified-Since and If-None-Match request header handling. */
        /* Heads up! Apache removes previously set Last-Modified header */
        /* from 304 Not Modified responses. */
        // if ($this->cache->isNotModified($request, $response)) {
        //     return $response->withStatus(304);
        // }
        if ($cache->isNotModified($request, $response)) {
            return $response->withStatus(304);
        }

        
        // if (false === $cart = $this->spot->mapper("App\Cart")->all()->where(["user_id" => $uid, "id"=>$cart_id])->execute()) {
        //     throw new ForbiddenException("Empty Shopping Cart.", 404);
        // }
        if (false === $cart = $spot->mapper("App\Cart")->all()->where(["user_id" => $uid, "id"=>$cart_id])->execute()) {
            throw new ForbiddenException("Empty Shopping Cart.", 404);
        }


        /* Serialize the response data. */
        $fractal = new Manager();
        $fractal->setSerializer(new DataArraySerializer);
        $cartTransformer = new CartTransformer();
        $cartTransformer->setMiddleUrl(getenv("MIDDLE_URL"));
        $resource = new Collection($cart, $cartTransformer);
        $adjustment = [];
        $cart->adjustments = $adjustment;
        $cartData = $fractal->createData($resource)->toArray();

        $cr = $cartData["data"];

        $payload = [];
        $totalAmount=0;

        for ($i=0; $i <count($cr) ; $i++) { 
            $element = $cr[$i];
            $totalAmount += $element["grand_total"];
        }

        $promoitems = [];


        if (empty($promo)==false) {
            for ($i=0; $i <count($promo) ; $i++) { 
                $this->logger->addInfo($promo[$i]);
                # code...
                array_push($promoitems,["promoCodeId"=>$promo[$i], "transactionAmount"=> $totalAmount,"outletId"=>$cr[0]["outlet_id"], "productId"=> $cr[0]["items"][0]["product_id"]]);
            }
        }


        $client = new GuzzleHttp\Client();
        $promotrx=[];
            //code...

        if(count($promoitems)>0) {
        
            $respromo = $client->request('POST', $_ENV["STAG_MAIN_DOMAIN"] . '/promo-code-service/cart', [
            // $respromo = $client->request('POST', 'http://127.0.0.1/promo-code-service/cart', [    
                "headers" => [
                    'Content-Type' => 'application/json',
                    'Authorization' => $request->getHeaderLine('Authorization')
                ],
                "body" => json_encode([
                    "items" => $promoitems
                ])
            ]);
            $this->logger->addInfo(' [x] Status Code :' . $respromo->getStatusCode());
            $this->logger->addInfo(' [x] Body :' . $respromo->getBody());
            $responsedata = $respromo->getBody();
            $responsedatas = json_decode($responsedata);
            
            if( $respromo->getStatusCode()!=201){
                throw new Exception("Error Processing Request", 1);
                
            }else{
                
                for ($i=0; $i <count($responsedatas->data->promoCartItems) ; $i++) { 
                    # code...
                    $element=$responsedatas->data->promoCartItems[$i];
                    array_push($promotrx,["promoId"=>$element->promoCode->promoCodeId,"code"=>$element->promoCode->code,"amount"=>$element->amount]);
                }

            }
        }
        
        for ($i=0; $i <count($cr) ; $i++) { 
            $element = $cr[$i];

            $keranjang = [
                "outlet_id"=>$element["outlet_id"],
                "sub_total"=>$element["sub_total"],
                "grand_total"=>$element["grand_total"],
            ];


            $py_items = [];
            for ($j=0; $j <count($element["items"]); $j++) { 
                $item = $element["items"][$j];
                // $productRef = $this->spot->mapper("App\Product")->all()->where(["guid" => $item->product_id])->first();
                $productRef = $spot->mapper("App\Product")->all()->where(["guid" => $item->product_id])->first();
                
                $res = $client->request('POST', $_ENV["STAG_MAIN_DOMAIN"] . '/trx-service/get-booked', [
                    "headers" => [
                        'Content-Type' => 'application/json'
                    ],
                    "body" => json_encode([
                        "product_id" => $item->product_id,
                        "check_in" => $item->check_in->format("Y-m-d H:i:s"),
                        "check_out" => $item->check_out->format("Y-m-d H:i:s"),
                        "DB_NAME"=>$_ENV["STAG_DBtrx"]
                    ])
                ]);


                $this->logger->addInfo(' [x] Product ID :' .   $item->product_id);
                $this->logger->addInfo(' [x] Status Code :' . $res->getStatusCode());
                $this->logger->addInfo(' [x] Body :' . $res->getBody()->__toString());
                $responsedata = $res->getBody()->__toString();
                $responsedatas = json_decode($responsedata);
                $booked = $responsedatas->booked;

                if($productRef->stock<=$booked){
                    throw new ForbiddenException("Stock habis.", 403);
                    break;
                }
                $it = [
                    "product_id"=>$item->product_id,
                    "price"=>$item->price,
                    "qty"=>$item->qty,
                    "note"=> "",
                    "check_in"=>$item->check_in->format("Y-m-d H:i:s"),
                    "check_out"=>$item->check_out->format("Y-m-d H:i:s"),
                    "product_name"=>$item->product_name,
                ];
                array_push($py_items, $it);

            }
            $keranjang["items"]= $py_items;
            array_push($payload, $keranjang);
            // $this->spot->mapper("App\CartItems")->query("DELETE FROM cartitems WHERE cart_id = ?", [$element["cart_id"]]);
            // $this->spot->mapper("App\Cart")->query("DELETE FROM cart WHERE id = ?", [$element["cart_id"]]);
            $spot->mapper("App\CartItems")->query("DELETE FROM cartitems WHERE cart_id = ?", [$element["cart_id"]]);
            $spot->mapper("App\Cart")->query("DELETE FROM cart WHERE id = ?", [$element["cart_id"]]);
        }


        $res = $client->request('POST', getenv("MAIN_DOMAIN") . '/trx-service/transaction-submit-v2', [
            "headers" => [
                'Content-Type' => 'application/json',
                'Authorization' => $request->getHeaderLine('Authorization')
            ],
            "body" => json_encode([
                "transaction" => $payload,
                "promo"=>$promotrx,
                "DB_NAME"=>$_ENV["STAG_DBtrx"]
            ])
        ]);
        $this->logger->addInfo(' [x] Status Code :' . $res->getStatusCode());
        $this->logger->addInfo(' [x] Body :' . $res->getBody());
        $responsedata = $res->getBody();
        $responsedatas = json_decode($responsedata);
        

        return $response->withStatus(200)
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($responsedatas, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    
    } catch (\Throwable $th) {
        throw $th;
    }
});
