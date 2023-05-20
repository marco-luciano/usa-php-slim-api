<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Respect\Validation\Validator as v;

$app->get('/states', function (Request $request, Response $response) {

    try {
        $query = "select s.state_id, s.name, s.abbreviation, SUM(c.population) as population  
            from counties c inner join states s on c.state_id = s.state_id  group by s.state_id order by s.name";

        $db = $this->get('db');
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = [];
        $result['status'] = "ok";
        $result['states'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response = new \Slim\Psr7\Response(200);
        $response->getBody()->write(formatOutput($result));

        return $response;
    } catch (Exception $e) {
        $result = [];
        $result['status'] = "error";
        $result['description'] = "Internal server error";
        $response = new \Slim\Psr7\Response(500);
        $response->getBody()->write(formatOutput($result));

        return $response;
    }
});


$app->get('/states/{state_id}/counties', function (Request $request, Response $response, $args) {
    try {
        // There are 51 state_id's = 50 States + DC
        v::intVal()->between(1,51)->setName("state_id")->assert($args['state_id']);

        $db = $this->get('db');
        $stmt = $db->prepare('select c.county_id, c."name", c.population  
        from counties c inner join states s on c.state_id = s.state_id 
        where s.state_id=:state_id');

        $stmt->bindParam(':state_id', $args['state_id']);
        $stmt->execute();

        $result = [];
        $result['status'] = "ok";
        $result['counties'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
