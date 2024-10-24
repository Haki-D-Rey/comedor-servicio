<?php

namespace App\Helpers;

use App\Middlewares\SaveRefererMiddleware;
use DirectoryIterator;
use Slim\App;

return function (App $app) {
    $app->add(SaveRefererMiddleware::class);
    $routesPath = __DIR__ . '/../Routes';
    $iterator = new DirectoryIterator($routesPath);

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $route = require $file->getPathname();
            $route($app);
        }
    }

    $routes = $app->getRouteCollector()->getRoutes();
    foreach ($routes as $route) {
        error_log($route->getPattern()); // Para verificar las rutas cargadas
    }
};
