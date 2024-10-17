<?php

use Slim\Factory\AppFactory;
use Selective\BasePath\BasePathMiddleware;
use Slim\Routing\RouteParser;
use Doctrine\DBAL\Types\Type;
use Ramsey\Uuid\Doctrine\UuidType;
use Doctrine\DBAL\Types\JsonType;

require_once __DIR__ . '/../vendor/autoload.php'; // Asegúrate de que esta línea esté presente

// Configura el contenedor de dependencias
define('CONFIG_PATH', __DIR__ . '/../configs');
$container = require_once CONFIG_PATH . '/container.php';
AppFactory::setContainer($container);

$app = AppFactory::create();
$app->addRoutingMiddleware();
$app->add(new BasePathMiddleware($app));
$app->addErrorMiddleware(true, true, true);
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

// Cargar rutas
$loadRoutes = require __DIR__ . '/../src/Helpers/loadRoutes.php';
$loadRoutes($app);

// Registra RouteParser después de cargar las rutas
$container->set(RouteParser::class, function () use ($app) {
    return $app->getRouteCollector()->getRouteParser();
});

// if (!Type::hasType('uuid')) {
//     Type::addType('uuid', UuidType::class);
// }

// if (!Type::hasType('jsonb')) {
//     Type::addType('jsonb', JsonType::class);
// }

date_default_timezone_set('America/Guatemala');
$app->run();
