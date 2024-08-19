<?php

use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use Selective\BasePath\BasePathMiddleware;

require_once __DIR__ . '/../vendor/autoload.php';

// Crear el contenedor de dependencias
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../src/Helpers/dependencias.php'); // Ajusta la ruta a tu archivo dependencies.php
$container = $containerBuilder->build();

// Configurar la aplicación Slim con el contenedor
AppFactory::setContainer($container);

// Obtener la configuración y añadirla al contenedor
$settings = require __DIR__ . '/settings.php'; // Ajusta la ruta a tu archivo settings.php
if (empty($settings)) {
    throw new Exception('La configuración no se cargó correctamente.');
}
$container->set('settings', $settings);

var_dump($container->get('settings'));
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

