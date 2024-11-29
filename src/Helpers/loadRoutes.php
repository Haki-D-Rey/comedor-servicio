<?php

namespace App\Helpers;

use App\Middlewares\SaveRefererMiddleware;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use Slim\App;

return function (App $app) {
    $app->add(SaveRefererMiddleware::class);
    $routesPath = __DIR__ . '/../Routes';
    
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($routesPath));
    $phpFiles = new RegexIterator($iterator, '/\.php$/'); // Only .php files

    foreach ($phpFiles as $file) {
        $route = require $file->getPathname();
        $route($app);
    }

    $routes = $app->getRouteCollector()->getRoutes();
    foreach ($routes as $route) {
        error_log($route->getPattern());
    }
};
