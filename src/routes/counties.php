<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy;
/**
 *@OA\Tag(name="Counties", description="")
 */


/**
 * @OA\Get(
 *     path="/counties",
 *     tags={"Counties"},
 *     @OA\Response(response="200", 
 * description="Creates a user")
 * )
 */

$app->group('/counties', function (RouteCollectorProxy $group) {
    $group->get('/', function () {

    });

    $group->post('', function ($id) {
    });

/**
 * @OA\Put(
 *     path="/counties",
 *     tags={"Counties"},
 *     @OA\Response(response="200", 
 * description="Updates county population")
 * )
 */

    $group->put('', function ($id) {
    });

    $group->delete('/:id', function ($id) {
    });
 });




