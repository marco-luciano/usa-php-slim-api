<?php

require dirname(__DIR__) . '/classes/CountyValidator.php';

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

    $group->post('', function (Request $request, Response $response) {
        try {
            $data = json_decode($request->getBody()->getContents(), true);
            $userValidator = new CountyValidator($data);
            $userValidator->assert($data);

            // Data OK, Save the user information to database
            $db = $this->get('db');
            $stmt = $db->prepare('INSERT INTO counties (name, state_id, population) VALUES (:name, :state_id, :population) RETURNING county_id, date_add');
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':state_id', $data['state_id']);
            $stmt->bindParam(':population', $data['population']);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $responseMessage = [
                "status" => 'ok',
                "county" => [
                    "id" => $result[0]['county_id'],
                    "name" => $data['name'],
                    "state_id" => $data['state_id'],
                    "population" => $data['population'],
                    "date" => $result[0]['date_add']
                ]
            ];

            $response->getBody()->write(formatOutput($responseMessage));
            return $response->withStatus(201);
        } catch (Respect\Validation\Exceptions\ValidatorException $e) {
            $response->getBody()->write($e->getFullMessage());
            return $response->withStatus(400);
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(400);
        }
    });

    $group->put('', function ($id) {
    });

    $group->delete('/:id', function ($id) {
    });
});
