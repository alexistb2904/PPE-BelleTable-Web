<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->setBasePath('/api');

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setErrorHandler(
    \Slim\Exception\HttpNotFoundException::class,
    function (Psr\Http\Message\ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails) use ($app) {
        $response = $app->getResponseFactory()->createResponse();
        $response->getBody()->write(json_encode([
            'error' => 'Route non trouvée',
            'status' => 404
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(404);
    }
);

require 'routes/group.php';
require 'routes/users.php';
require 'routes/questionnaires.php';

$app->get('/', function ($request, $response, $args) use ($app) {
    $routeCollector = $app->getRouteCollector();
    $routes = $routeCollector->getRoutes();

    $routesList = [];

    foreach ($routes as $route) {
        $routesList[] = [
            'methods' => $route->getMethods(),
            'pattern' => $route->getPattern()
        ];
    }

    $response->getBody()->write(json_encode($routesList));
    return $response->withHeader('Content-Type', 'application/json');
});


$app->run();
