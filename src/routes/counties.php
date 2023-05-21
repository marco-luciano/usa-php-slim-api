<?php

require dirname(__DIR__) . '/classes/CountyValidator.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy;
use \Respect\Validation\Validator as v;

$app->group('/counties', function (RouteCollectorProxy $group) {
    $group->get('/{county_id}', function (Request $request, Response $response, $args) {
        try {
            v::intVal()->min(1)->setName("county_id")->assert($args['county_id']);

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
            return $response->withStatus(200);
        } catch (Respect\Validation\Exceptions\ValidatorException $e) {
            $response->getBody()->write($e->getFullMessage());
            return $response->withStatus(400);
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(400);
        }
    });

    $group->put('/{county_id}', function (Request $request, Response $response, $args) {
        try {
            $data = json_decode($request->getBody()->getContents(), true);

            v::intVal()->min(1)->setName("county_id")->assert($args['county_id']);

            if (array_key_exists('population', $data)) {
                v::intVal()->min(0)->setName("population")->assert($data['population']);
            } else {
                $respMessage = [];
                $respMessage['status'] = "error";
                $respMessage['description'] = "Invalid request error";
                throw new Exception(formatOutput($respMessage));
            }

            $timestamp = (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d H:i.s0');

            // Data OK, Save the user information to database
            $db = $this->get('db');
            $stmt = $db->prepare('UPDATE counties SET 
                    population = :population, date_upd = :date_upd
                    WHERE county_id = :county_id RETURNING name, state_id, date_upd');
            $stmt->bindParam(':population', $data['population']);
            $stmt->bindParam(':date_upd', $timestamp);
            $stmt->bindParam(':county_id', $args['county_id']);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // count equals 1 on update, 0 no update
            $update = (bool)count($result);

            if ($update) {
                // update successful
                $responseMessage = [
                    "status" => 'ok',
                    "county" => [
                        "id" => $args['county_id'],
                        "name" => $result[0]['name'],
                        "state_id" => $result[0]['state_id'],
                        "population" => $data['population'],
                        "date_upd" => $result[0]['date_upd']
                    ]
                ];

                $response->getBody()->write(formatOutput($responseMessage));
                return $response->withStatus(200);
            } else {
                // non-existing county_id
                $respMessage = [];
                $respMessage['status'] = "error";
                $respMessage['description'] = "Invalid request error - country_id does not exist";
                throw new Exception(formatOutput($respMessage));
            }
        } catch (Respect\Validation\Exceptions\ValidatorException $e) {
            $response->getBody()->write($e->getFullMessage());
            return $response->withStatus(400);
        } catch (PDOException $e) {
            $respMessage = [];
            $respMessage['status'] = "error";
            $respMessage['description'] = "Internal server error";
            $response = new \Slim\Psr7\Response(500);
            $response->getBody()->write(formatOutput($respMessage));

            return $response;
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(400);
        }
    });

    $group->delete('/{county_id}', function (Request $request, Response $response, $args) {
        try {
            if (array_key_exists('county_id', $args)) {
                v::intVal()->min(1)->setName("county_id")->assert($args['county_id']);

                // county_id OK, county delete
                $db = $this->get('db');
                $stmt = $db->prepare('DELETE FROM counties WHERE county_id = :county_id');
                $stmt->bindParam(':county_id', $args['county_id']);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // count equals 1 on delete, 0 no update
                $delete = (bool)count($result);

                if ($delete) {
                    // update successful
                    $responseMessage = [
                        "status" => 'ok',
                        "description" => "county_id " . $args['county_id'] . " deleted successfully"
                    ];

                    $response->getBody()->write(formatOutput($responseMessage));
                    return $response->withStatus(200);
                } else {
                    // non-existing county_id
                    $respMessage = [];
                    $respMessage['status'] = "error";
                    $respMessage['description'] = "Invalid request error - country_id " . $args['county_id'] . " does not exist";
                    throw new Exception(formatOutput($respMessage));
                }
            } else {
                // non-existing county_id
                $respMessage = [];
                $respMessage['status'] = "error";
                $respMessage['description'] = "Invalid request error";
                throw new Exception(formatOutput($respMessage));
            }
        } catch (Respect\Validation\Exceptions\ValidatorException $e) {
            $response->getBody()->write($e->getFullMessage());
            return $response->withStatus(400);
        } catch (PDOException $e) {
            $respMessage = [];
            $respMessage['status'] = "error";
            $respMessage['description'] = "Internal server error";
            $response = new \Slim\Psr7\Response(500);
            $response->getBody()->write(formatOutput($respMessage));

            return $response;
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(400);
        }
    });
});
