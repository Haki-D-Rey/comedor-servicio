<?php

use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use Selective\BasePath\BasePathMiddleware;

require_once __DIR__ . '/../vendor/autoload.php'; // Asegúrate de que esta línea esté presente

$settings = require __DIR__ . '/settings.php';

// Configura el contenedor de dependencias
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../src/Helpers/dependencias.php');
$container = $containerBuilder->build();

// Configura el contenedor en la aplicación Slim
AppFactory::setContainer($container);

if (empty($settings)) {
    throw new Exception('La configuración no se cargó correctamente.');
}
$container->set('settings', $settings);

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

$app->run();
