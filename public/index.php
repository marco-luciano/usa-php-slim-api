<?php

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/src/classes/UserValidator.php';
require dirname(__DIR__) . '/src/classes/UserAuthValidator.php';
require dirname(__DIR__) . '/src/exceptions/UserAlreadyTakenException.php';
require dirname(__DIR__) . '/src/exceptions/InvalidCredentialsException.php';
require dirname(__DIR__) . '/src/utils/formatOutput.php';

use DI\Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface;
use Slim\Factory\AppFactory;
use Firebase\JWT\JWT;

/**
 * @OA\Info(title="USA States and Counties API", version="0.1")
 *
 * @OA\Server(
 *     url="http://localhost:8080",
 *     description="API server"
 * )
 */

/**
 * @OA\Get(
 *     path="/api/resource.json",
 *     @OA\Response(response="200", description="Example")
 * )
 */

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
            throw new Exception("Invalid authorization header. Bearer token expected.");
        }

        $decodedToken = JWT::decode($tokenArray[1], $secretKey);
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

/**
 * @OA\Post(
 *     path="/user",
 *     tags={"Users"},
 *     @OA\Response(response="201", 
 * description="Creates a user")
 * )
 */
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

/**
 * @OA\Get(
 *     tags={"Users"},
 *     path="/user/authentication",
 *     @OA\Response(response="200", 
 *      description="Authenticates a user returning a token")
 * )
 */

$app->get('/user/authentication', function (Request $request, ResponseInterface $response) {

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

        // Valid credentials. JWT generation
        $exp = new DateTime("now +2 hours");

        $payload = array(
            "exp" => $exp->getTimeStamp(),
            "user_id" => $result[0]['user_id']
        );

        $jwt = JWT::encode($payload, $_SERVER['SECRET_KEY'], "HS256");

        $data = [
            "status" => "ok",
            "description" => "Login successful",
            "token" => $jwt,
            "exp" => $exp->getTimeStamp()
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

require dirname(__DIR__) . '/src/routes/counties.php';
require dirname(__DIR__) . '/src/routes/states.php';

$app->add($jwtMiddleware);

$app->run();