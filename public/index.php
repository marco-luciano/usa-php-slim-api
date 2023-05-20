<?php

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/src/classes/UserValidator.php';
require dirname(__DIR__) . '/src/classes/UserAuthValidator.php';
require dirname(__DIR__) . '/src/exceptions/UserAlreadyTakenException.php';
require dirname(__DIR__) . '/src/exceptions/InvalidCredentialsException.php';
require dirname(__DIR__) . '/src/utils/formatOutput.php';
require dirname(__DIR__) . '/src/utils/JWTWithExpiration.php';

use DI\Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface;
use Slim\Factory\AppFactory;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * @OA\Info(
 *   title="USA States and Counties API",
 *   description="REST API for information retrieval about <b> states and counties of the United States Of America. </b> <br><br> Population numbers are 2022 estimates made by the United States Census Bureau
 *   <a href=https://www.census.gov/programs-surveys/popest.html>US Census Population and Housing Unit Estimates - Official website</a>",
 *   version="1.0.0",
 *   @OA\Contact(
 *     email="marco.magisano.7@gmail.com"
 *   )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8080",
 *     description="Development server"
 * )
 * 
 * @OA\SecurityScheme(
 *      type="http",
 *      name="jwt",
 *      in="header",
 *      securityScheme="bearerAuth",
 *      scheme="bearer",
 *      description="This API uses JSON Web Token for user authentication",
 *      bearerFormat="JWT",
 * )
 **/

$app = AppFactory::create();

// Create DI container
$container = new Container();

// Create Slim app
AppFactory::setContainer($container);
$app = AppFactory::create();

// Set up the PostgreSQL database connection
$container->set('db', function () {

    try {
        $host = $_SERVER['POSTGRES_HOST'] ?? "db";
        $db_name = $_SERVER['POSTGRES_DB'];
        $password = $_SERVER['POSTGRES_PASSWORD'];
        // Create a new PDO instance
        $pdo = new PDO("pgsql:host=$host;dbname=$db_name", $_SERVER['POSTGRES_USER'], $password);

        // Set PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
});

$secretKey = $_SERVER['SECRET_KEY'];

// Add middleware to validate JWT
$jwtMiddleware = function (Request $request, $handler) use ($secretKey) {

    $path = $request->getUri()->getPath();

    if ($path === '/user' || $path === '/user/authentication') {
        return $handler->handle($request);
    }
    // Retrieve the token from the request headers
    $token = $request->getHeaderLine('Authorization');

    // Validate and decode the token
    try {
        $tokenArray = explode(' ', $token);

        if ($tokenArray[0] !== "Bearer") {

            $responseMessage = [
                "status" => 'error',
                "description" => "Invalid authorization header. Bearer token expected."
            ];
            
            throw new Exception(formatOutput($responseMessage));
        }

        $decodedToken = JWT::decode($tokenArray[1], new Key($secretKey, 'HS256'));

        $timestamp = (new DateTime('now'))->getTimestamp();

        //check for fields
        if (isset($decodedToken->exp) && isset($decodedToken->user_id)) {
            //check for user_id and token expiration
            if ($decodedToken->exp > $timestamp) {
                //token correct
                // check if the user is already registered

                $db = $this->get('db');
                $stmt = $db->prepare('SELECT * FROM users WHERE user_id = :user_id');
                $stmt->bindParam(':user_id', $decodedToken->user_id);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (isset($result[0]['user_id'])) {
                    //token validation successful

                } else {
                    $responseMessage = [
                        "status" => 'error',
                        "description" => "Invalid token"
                    ];

                    throw new Exception(formatOutput($responseMessage));
                }
            } else {
                //token expired
                $responseMessage = [
                    "status" => 'error',
                    "description" => "Bearer token expired"
                ];

                throw new Exception(formatOutput($responseMessage));
            }
        } else {
            $responseMessage = [
                "status" => 'error',
                "description" => "Invalid token"
            ];

            throw new Exception(formatOutput($responseMessage));
        }

        // Pass the decoded token to the request attributes for further use
        $request = $request->withAttribute('token', $decodedToken);
    } catch (Exception $e) {
        // Handle token validation errors, e.g., return an error response
        $response = new \Slim\Psr7\Response(401);
        $response->getBody()->write($e->getMessage());
        return $response;
    }

    // Call the next middleware/handler
    return $handler->handle($request);
};

$app->post('/user', function (Request $request, ResponseInterface $response) {

    try {
        $data = json_decode($request->getBody()->getContents(), true);

        $userValidator = new UserValidator($data);
        $userValidator->assert($data);

        // Encrypt the password
        $encryptedPassword = hash('sha3-512', $data['password']);

        // check if the user is already registered
        $db = $this->get('db');
        $stmt = $db->prepare('SELECT COUNT(*) as users_qty FROM users WHERE name = :username');
        $stmt->bindParam(':username', $data['username']);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // users_qty is 0 if is not registered, hence false, and 1 if is already registered, hence true
        $isAlreadyRegistered = (bool)($result[0]['users_qty']);

        if ($isAlreadyRegistered) {
            $responseMessage = [
                "status" => 'error',
                "description" => sprintf("Username %s is already taken", $data['username'])
            ];

            throw new UserAlreadyTakenException(formatOutput($responseMessage));
        }

        // Save the user information in the database
        $stmt = $db->prepare('INSERT INTO users (name, password) VALUES (:username, :password)');
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':password', $encryptedPassword);
        $stmt->execute();

        $responseMessage = [
            "status" => 'ok',
            "description" => 'User registered successfully'
        ];

        $response->getBody()->write(formatOutput($responseMessage));
        return $response->withStatus(201);
    } catch (Respect\Validation\Exceptions\ValidatorException $e) {
        $response->getBody()->write($e->getFullMessage());
        return $response->withStatus(400);
    } catch (UserAlreadyTakenException $e) {
        $response->getBody()->write($e->getMessage());
        return $response->withStatus(409);
    } catch (Exception $e) {
        $response->getBody()->write($e->getMessage());
        return $response->withStatus(400);
    }
});

$app->post('/user/authentication', function (Request $request, ResponseInterface $response) {

    try {
        $data = json_decode($request->getBody()->getContents(), true);
        $userValidator = new UserAuthValidator($data);
        $userValidator->assert($data);

        // Encrypt the password
        $encryptedPassword = hash('sha3-512', $data['password']);

        // Save the user information in the database
        $db = $this->get('db');
        $stmt = $db->prepare('SELECT * FROM users WHERE name = :username AND password = :passwd');
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':passwd', $encryptedPassword);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $validCredentials = (bool)(count($result));

        if (!$validCredentials) {
            $responseMessage = [
                "status" => 'error',
                "description" => "invalid credentials"
            ];

            throw new InvalidCredentialsException(formatOutput($responseMessage));
        }

        $jwt = JWTWithExpiration($result[0]['user_id']);

        $data = [
            "status" => "ok",
            "description" => "Login successful",
            "token" => $jwt['jwt'],
            "exp" => $jwt['exp']
        ];

        $payload = json_encode($data);

        $response->getBody()->write($payload);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    } catch (Respect\Validation\Exceptions\ValidatorException $e) {
        $response->getBody()->write($e->getFullMessage());
        return $response->withStatus(400);
    } catch (InvalidCredentialsException $e) {
        $response->getBody()->write($e->getMessage());
        return $response->withStatus(401);
    } catch (Exception $e) {
        $response->getBody()->write($e->getMessage());
        return $response->withStatus(400);
    }
});

$app->get('/user/authentication', function (Request $request, ResponseInterface $response) {

    $jwt = JWTWithExpiration(1);
    $response->getBody()->write($jwt['jwt']);

    return $response->withStatus(200);
});

require dirname(__DIR__) . '/src/routes/counties.php';
require dirname(__DIR__) . '/src/routes/states.php';

$app->add($jwtMiddleware);
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->run();
