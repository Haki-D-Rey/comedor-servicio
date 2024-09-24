<?php

use Slim\App;
use App\Controllers\ServiciosProductosDetallesController;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('/servicios-productos-detalles', function (RouteCollectorProxy $group) {
        // Rutas estáticas
        $group->get('/all', ServiciosProductosDetallesController::class . ':getAllServiciosProductosDetalles');

        // Rutas dinámicas
        $group->get('/{id}', ServiciosProductosDetallesController::class . ':getServiciosProductosDetallesById');
        $group->post('/', ServiciosProductosDetallesController::class . ':createServiciosProductosDetalles');
        $group->put('/{id}', ServiciosProductosDetallesController::class . ':updateServiciosProductosDetalles');
        $group->delete('/{id}', ServiciosProductosDetallesController::class . ':deleteServiciosProductosDetalles');
    });

};
