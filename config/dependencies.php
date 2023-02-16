<?php

$container = $app->getContainer();
use Spot\Config;
use Spot\Locator;
use Doctrine\DBAL\Logging\MonologSQLLogger;

$container["spot"] = function ($container) {

    $config = new Config();
    $db = $config->addConnection($_ENV['STAG_DB_DRIVER'], [
        "dbname" => $_ENV["STAG_DBmerchant"],
        "user" => $_ENV["STAG_DB_USERNAME"],
        "password" => $_ENV["STAG_DB_PASSWORD"],
        "host" => $_ENV["STAG_DB_HOST"],
        "driver" => "pdo_".$_ENV["STAG_DB_DRIVER"],
        "charset" => "utf8"
    ]);

    $spot = new Locator($config);

    $logger = new MonologSQLLogger($container["logger"]);
    $db->getConfiguration()->setSQLLogger($logger);

    return $spot;
};

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;


$container["logger"] = function ($container) {
    // logger aplikasi
    $logger = new Logger("slim");
    $dateFormat = "Y n j, g:i a";
    $output = "%datetime%  %level_name%  %message% %context% %extra%\n";
    // $formatter = new LineFormatter(
    //     "[%datetime%] [%level_name%]: %message% %context%\n", null, true, true
    // );
    $formatter = new LineFormatter( $output, $dateFormat);

    /* Log to timestamped files */
    $rotating = new StreamHandler(__DIR__.'/../logs/slim.log', Logger::DEBUG);
    // $rotating = new RotatingFileHandler(__DIR__ . "/../logs/slim.log", 0, Logger::DEBUG);
    $rotating->setFormatter($formatter);
    $logger->pushHandler($rotating);

    return $logger;
};

$container["notAllowedHandler"] = function ($container) {
    return function ($request, $response, $methods) use ($container) {
        // pesan untuk kesalahan method
        return $response->withStatus(405)
            ->withHeader('Allow', implode(', ', $methods))
            ->withHeader('Content-type', 'text/html')
            ->write("Method must be one of: " . implode(', ', $methods));
    };
};
