<?php

namespace App\Helpers;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\DataArraySerializer;

/**
 * Format response.
 */
class FormatResponse
{
    
    /**
     * API Response
     *
     * @var array
     */
    protected static $response = [
        'status' => "ok",
        'message' => null,
    ];

    /**
     * Give success response.
     */
    public static function List($response,$data = null,$meta=null)
    {
        self::$response['status'] = "ok";
        self::$response['message'] = "Success list Item";
        self::$response['meta'] = $meta;
        self::$response['data'] = $data;


        return $response->withJson(self::$response, 200);
    }

    /**
     * Give item response.
     */

    public static function Item($response,$message=null,$data = null)
    {
        self::$response['status'] = "ok";
        self::$response['message'] = $message;
        self::$response['data'] = $data;

        return $response->withJson(self::$response, 200);
    }

    public static function Delete($response)
    {
        self::$response['status'] = "ok";
        self::$response['message'] = "Success Delete Item";

        return $response->withJson(self::$response, 200);
    }


    /**
     * Give error response.
     */
    public static function error($response,$message = null, $code = 400)
    {
        self::$response['status'] = "error";
        self::$response['message'] = $message;

        return $response->withJson(self::$response, $code);
    }

}
