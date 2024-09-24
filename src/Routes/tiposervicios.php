<?php

use Slim\App;
use App\Controllers\TipoServiciosController;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('/tipo-servicios', function (RouteCollectorProxy $group) {
        // Rutas estáticas
        $group->get('/all', TipoServiciosController::class . ':getAllTipoServicios');

        // Rutas dinámicas
        $group->get('/{id}', TipoServiciosController::class . ':getTipoServicioById');
        $group->post('/', TipoServiciosController::class . ':createTipoServicio');
        $group->put('/{id}', TipoServiciosController::class . ':updateTipoServicio');
        $group->delete('/{id}', TipoServiciosController::class . ':deleteTipoServicio');
    });

};
