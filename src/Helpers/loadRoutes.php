<?php

namespace App\Helpers;

use App\Middlewares\SaveRefererMiddleware;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use Slim\App;

return function (App $app) {
    $app->add(SaveRefererMiddleware::class);

    // Ruta donde se encuentran los archivos de rutas
    $routesPath = __DIR__ . '/../Routes';

    // Crear un iterador recursivo para buscar archivos PHP en la carpeta Routes
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($routesPath));
    $phpFiles = new RegexIterator($iterator, '/\.php$/');

    foreach ($phpFiles as $file) {
        $route = require $file->getPathname();
        if (is_callable($route)) {
            $route($app);
        }
    }

    $routes = $app->getRouteCollector()->getRoutes();
    foreach ($routes as $route) {
        error_log($route->getPattern());
    }
};
