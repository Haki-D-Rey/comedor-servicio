<?php

use Slim\App;
use App\Controllers\ServiciosProductosDetallesController;
use Slim\Routing\RouteCollectorProxy;

    return function (App $app) {

        $app->group('/servicios-productos-detalles', function (RouteCollectorProxy $group) {
            // Rutas estáticas
            $group->get('/all', ServiciosProductosDetallesController::class . ':getAllServiciosProductosDetalles')
                ->setName('servicios_productos_detalles.list');

            // Rutas dinámicas
            $group->get('/{id}', ServiciosProductosDetallesController::class . ':getServiciosProductosDetallesById')
                ->setName('servicios_productos_detalles.view');

            $group->post('/', ServiciosProductosDetallesController::class . ':createServiciosProductosDetalles')
                ->setName('servicios_productos_detalles.create');

            $group->put('/{id}', ServiciosProductosDetallesController::class . ':updateServiciosProductosDetalles')
                ->setName('servicios_productos_detalles.update');

            $group->delete('/{id}', ServiciosProductosDetallesController::class . ':deleteServiciosProductosDetalles')
                ->setName('servicios_productos_detalles.delete');
        });
    };
