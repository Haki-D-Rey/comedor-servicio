<?php

use Slim\App;
use App\Controllers\TipoServiciosController;
use App\Middlewares\AuthorizationMiddleware;
use App\Middlewares\AuthMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('/tipo-servicios', function (RouteCollectorProxy $group) {
        // Rutas estáticas
        $group->get('/all', TipoServiciosController::class . ':getAllTipoServicios')->setName('tipo_servicios.list_all')
            ->add(AuthorizationMiddleware::class)
            ->setArgument('permission', 'tipo_servicios.list_all');

        // Rutas dinámicas
        $group->get('/{id}', TipoServiciosController::class . ':getTipoServicioById')->setName('tipo_servicios.view');
        $group->post('/', TipoServiciosController::class . ':createTipoServicio')->setName('tipo_servicios.create');
        $group->put('/{id}', TipoServiciosController::class . ':updateTipoServicio')->setName('tipo_servicios.update');
        $group->delete('/{id}', TipoServiciosController::class . ':deleteTipoServicio')->setName('tipo_servicios.delete');
    })->add(AuthMiddleware::class);
};
