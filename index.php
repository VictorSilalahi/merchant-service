<?php

# atur time zone
date_default_timezone_set("Asia/Jakarta");

# autoload semua library
require __DIR__ . "/vendor/autoload.php";

# baca .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = new \Slim\App([
    "settings" => [
        "displayErrorDetails" => true,
    ]
]);

require __DIR__ . "/config/db.php";
require __DIR__ . "/config/functions.php";
require __DIR__ . "/config/dependencies.php";
require __DIR__ . "/config/handlers.php";
require __DIR__ . "/config/middleware.php";

$app->get("/", function ($request, $response, $args) {
    print "DNA Merchant Service Api!";
});

require __DIR__ . "/routes/user.php";
require __DIR__ . "/routes/merchant.php";
require __DIR__ . "/routes/report.php";
require __DIR__ . "/routes/balance.php";

$app->run();
