<?php

return [

    // GENERAL ERROR
    "SUCCESS"       => [
        "STATUS"    => TRUE,
        "CODE"      => "0000",
        "MESSAGE"   => "SUCCESS",
        "HTTP_CODE" => 200
    ],
    "SERVER_ERROR"  => [
        "STATUS"    => FALSE,
        "CODE"      => "0001",
        "MESSAGE"   => "INTERNAL SERVER ERROR",
        "HTTP_CODE" => 500
    ],
    "BAD_REQUEST"   => [
        "STATUS"    => FALSE,
        "CODE"      => "0002",
        "MESSAGE"   => "BAD REQUEST",
        "HTTP_CODE" => 400
    ],
    "RESOURCE_NOT_FOUND"   => [
        "STATUS"    => FALSE,
        "CODE"      => "0003",
        "MESSAGE"   => "RESOURCE NOT FOUND",
        "HTTP_CODE" => 404
    ],
    "METHOD_NOT_ALLOWED"   => [
        "STATUS"    => FALSE,
        "CODE"      => "0004",
        "MESSAGE"   => "METHOD NOT ALLOWED",
        "HTTP_CODE" => 405
    ],


    // AUTHENTICATION ERROR
    "WRONG_CREDENTIAL"   => [
        "STATUS"    => FALSE,
        "CODE"      => "1001",
        "MESSAGE"   => "WRONG EMAIL OR PASSWORD",
        "HTTP_CODE" => 401
    ],
    "USER_NOT_FOUND"   => [
        "STATUS"    => FALSE,
        "CODE"      => "1002",
        "MESSAGE"   => "USER NOT FOUND",
        "HTTP_CODE" => 404
    ],
    "TOKEN_EXPIRED"   => [
        "STATUS"    => FALSE,
        "CODE"      => "1003",
        "MESSAGE"   => "TOKEN EXPIRED",
        "HTTP_CODE" => 419
    ],
    "TOKEN_INVALID"   => [
        "STATUS"    => FALSE,
        "CODE"      => "1004",
        "MESSAGE"   => "TOKEN INVALID",
        "HTTP_CODE" => 401
    ],
];
