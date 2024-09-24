<?php

use Slim\App;
use App\Controllers\ServiciosProductosController;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('/servicios-productos', function (RouteCollectorProxy $group) {
        // Rutas estáticas
        $group->get('/all', ServiciosProductosController::class . ':getAllServiciosProductos');

        // Rutas dinámicas
        $group->get('/{id}', ServiciosProductosController::class . ':getServiciosProductoById');
        $group->post('/', ServiciosProductosController::class . ':createServiciosProducto');
        $group->put('/{id}', ServiciosProductosController::class . ':updateServiciosProducto');
        $group->delete('/{id}', ServiciosProductosController::class . ':deleteServiciosProducto');
    });

};
