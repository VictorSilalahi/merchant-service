<?php

use Micheh\Cache\CacheUtil;
use Firebase\JWT\JWT;

function getUserId($token)
{
    $t = str_replace("Bearer ","",$token);
    $dat = JWT::decode($t, $_ENV["STAG_JWT_SECRET"], array('HS256'));
    return $dat->uid;
}

function getUserName($token)
{
    $t = str_replace("Bearer ","",$token);
    $dat = JWT::decode($t, $_ENV["STAG_JWT_SECRET"], array('HS256'));
    return $dat->sub;
}

function getOutlets($token)
{
    $t = str_replace("Bearer ","",$token);
    $dat = JWT::decode($t, $_ENV["STAG_JWT_SECRET"], array('HS256'));
    return json_decode($dat->outlets);
}

function makeSpot($dbname) {
    $cfg = new \Spot\Config();

    $cfg->addConnection('pgsql', [
        'dbname' => $dbname,
        'user' => $_ENV["STAG_DB_USERNAME"],
        'password' => $_ENV["STAG_DB_PASSWORD"],
        'host' => $_ENV["STAG_DB_HOST"],
        'driver' => 'pdo_'.$_ENV["STAG_DB_DRIVER"]
    ]);

    $spot = new \Spot\Locator($cfg);

    return $spot;
}

function makeCache() {
    return new CacheUtil;
}