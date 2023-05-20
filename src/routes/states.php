<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

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
