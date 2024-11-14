<?php

use Slim\App;
use App\Controllers\DashboardController;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\AuthorizationMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('/dashboard', function (RouteCollectorProxy $group) {
        // Rutas estáticas
        $group->get('/', DashboardController::class . ':index')
            ->setName('dashboard.index')
            ->add(AuthorizationMiddleware::class)
            ->setArgument('permission', 'dashboard_view');

        $group->group('/formularios', function (RouteCollectorProxy $form) {
            $form->get('/control-estadisticos', DashboardController::class . ':formulario');
        });
    })->add(AuthMiddleware::class);
};
