<?php

use Slim\App;
use App\Controllers\DashboardController;
use App\Controllers\Publico\VentasController;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\AuthorizationMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    // Dashboard group
    $app->group('/dashboard', function (RouteCollectorProxy $group) {

        // Dashboard index route
        $group->get('/', DashboardController::class . ':index')
            ->setName('dashboard.index')
            ->add(AuthorizationMiddleware::class) // AuthorizationMiddleware for permission check
            ->setArgument('permission', 'dashboard_view'); // permission argument for the middleware

        // Form group (specific to forms)
        $group->group('/formularios', function (RouteCollectorProxy $form) {
            $form->get('/control-estadisticos', DashboardController::class . ':formulario');
        });
        
         $group->group('/reportes', function (RouteCollectorProxy $reportes) {
            $reportes->get('/', DashboardController::class . ':reportes_index')->setName('dashboard.reportes_index');
        });


        // Facturación group
        $group->group('/facturacion', function (RouteCollectorProxy $ventas) {

            // Facturación index route
            $ventas->get('/', VentasController::class . ':index')
                ->setName('facturacion.index')
                ->setArgument('permission', 'facturacion_view') // Permission argument for authorization
                ->add(AuthorizationMiddleware::class); // AuthorizationMiddleware for permission check

        });
    })->add(AuthMiddleware::class); // AuthMiddleware applies to all routes under /dashboard

};
