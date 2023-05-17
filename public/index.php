<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Factory\AppFactory;

/**
 * @OA\Info(title="USA States and Counties API", version="0.1")
 */

/**
 * @OA\Get(
 *     path="/api/resource.json",
 *     @OA\Response(response="200", description="Example")
 * )
 */

$app = AppFactory::create();
$app->log->setEnabled(true);

$host = $_SERVER['POSTGRES_HOST'] ?? "db";
$db_name = $_SERVER['POSTGRES_DB'];
$password = $_SERVER['POSTGRES_PASSWORD'];

try {
    // Create a new PDO instance
    $pdo = new PDO("pgsql:host=$host;dbname=$db_name", $_SERVER['POSTGRES_USER'], $password);

    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to the PostgreSQL database successfully!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

$app->get('/', function (RequestInterface $request, ResponseInterface $response, array $args) {
    $response->getBody()->write('Hello from Slim 4 request handler');
    return $response;
});

$app->run();