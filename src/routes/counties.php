<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy;
use \Respect\Validation\Validator as v;


$app->group('/counties', function (RouteCollectorProxy $group) {
    $group->get('/{county_id}', function (Request $request, Response $response, $args) {
        try {
            v::intVal()->setName("county_id")->assert($args['county_id']);
            
            $db = $this->get('db');
            $stmt = $db->prepare(
                'select county_id, name, state_id, population from counties where county_id = :county_id'
            );

            $stmt->bindParam(':county_id', $args['county_id']);
            $stmt->execute();

            $result = [];
            $result['status'] = "ok";
            $result['county'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $response->getBody()->write(formatOutput($result));
            return $response->withStatus(200);

        } catch (Respect\Validation\Exceptions\ValidatorException $e) {

            $response->getBody()->write($e->getFullMessage());
            return $response->withStatus(400);
        } catch (Exception $e) {
            $result = [];
            $result['status'] = "error";
            $result['description'] = "Internal server error";
            $response = new \Slim\Psr7\Response(500);
            $response->getBody()->write(formatOutput($result));

            return $response;
        }
    });

    $group->post('', function ($id) {
    });

    $group->put('', function ($id) {
    });

    $group->delete('/:id', function ($id) {
    });
});
