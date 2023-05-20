<?php

use Firebase\JWT\JWT;

function JWTWithExpiration($text)
{
    // Valid credentials. JWT generation
    $exp = new DateTime("now +2 hours");

    $payload = array(
        "exp" => $exp->getTimeStamp(),
        "user_id" => $text
    );

    $jwt = JWT::encode($payload, $_SERVER['SECRET_KEY'], "HS256");

    $response = [
        'jwt' => $jwt,
        'exp' => $exp->getTimestamp()
    ];

    return $response;
}
