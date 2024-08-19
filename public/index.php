<?php

use Slim\Factory\AppFactory;
use Selective\BasePath\BasePathMiddleware;

require_once __DIR__ . '/../vendor/autoload.php';
$app = AppFactory::create();


// Configuración del contenedor de dependencias
$container = $app->getContainer();
$settings = require __DIR__ . '/settings.php';
$container->set('settings', $settings);

// Incluir archivo de configuración del contenedor
$dependencies = require __DIR__ . '/dependencies.php';
foreach ($dependencies as $key => $factory) {
    $container->set($key, $factory($container));
}

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

