<?php

use Tuupola\Middleware\JwtAuthentication;
use Tuupola\Middleware\CorsMiddleware;
use Gofabian\Negotiation\NegotiationMiddleware;

$container = $app->getContainer();

$container["JwtAuthentication"] = function ($container) {
    // autentikasi menggunakan jwt
    return new JwtAuthentication([
        "path" => "/v1",
        "ignore" => [
            "/v1/user/login",
        ],
        "secret" => $_ENV["STAG_JWT_SECRET"],
        "logger" => $container["logger"],
        "relaxed" => explode(",", $_ENV["STAG_RELAXED"]),
        "error" => function ($response, $arguments) {
            $data["status"] = "error";
            $data["message"] = $arguments["message"];
            return $response
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }
    ]);
};

$container["Cors"] = function ($container) {
    // allow CORS
    return new CorsMiddleware([
        "logger" => $container["logger"],
        "origin" => ["*"],
        "methods" => ["GET", "POST", "PUT", "PATCH", "DELETE", "OPTIONS", "HEAD"],
        "headers.allow" => ["Authorization", "If-Match", "If-Unmodified-Since"],
        "headers.expose" => ["Authorization", "Etag"],
        "credentials" => true,
        "cache" => 60,
        "error" => function ($request, $response, $arguments) {
            $data["status"] = "error";
            $data["message"] = $arguments["message"];
            return $response
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }
    ]);
};


$container["Negotiation"] = function ($container) {
    return new NegotiationMiddleware([
        "accept" => ["application/json"]
    ]);
};


$app->add("JwtAuthentication");
$app->add("Cors");
$app->add("Negotiation");

