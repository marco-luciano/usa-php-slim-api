<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 *@OA\Tag(name="States", description="")
 */

/**
 * @OA\Get(
 *     path="/states",
 *     tags={"States"},
 *     @OA\Response(response="200", 
 * description="Creates a user")
 * )
 */

$app->get('/states', function ($id) {

});

/**
 * @OA\Post(
 *     path="/states",
 *     tags={"States"},
 *     @OA\Response(response="200", 
 * description="Creates a user")
 * )
 */

$app->post('/states', function ($id) {
});
