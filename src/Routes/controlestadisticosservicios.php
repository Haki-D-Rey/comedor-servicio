<?php

use Slim\App;
use App\Controllers\ControlEstadisticosServiciosController;
use App\Middlewares\AuthMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('/control-estadisticos-servicios', function (RouteCollectorProxy $group) {
        // Rutas estáticas
        $group->get('/all', ControlEstadisticosServiciosController::class . ':getAllControlEstadisticosServicios')
            ->setName('control-estadisticos-servicios.list_all');

        $group->get('/filter-date-search/{date}', ControlEstadisticosServiciosController::class . ':getControlEstadisticosServiciosByIdForDate')
            ->setName('control-estadisticos-servicios.search_date');

        $group->post('/create-form', ControlEstadisticosServiciosController::class . ':createFormControlEstadisticosServicios')
            ->setName('control-estadisticos-servicios.create_form');

        // Rutas dinámicas
        $group->get('/{id}', ControlEstadisticosServiciosController::class . ':getControlEstadisticosServiciosById')
            ->setName('control-estadisticos-servicios.get_id');

        $group->post('/', ControlEstadisticosServiciosController::class . ':createControlEstadisticosServicios')
            ->setName('control-estadisticos-servicios.create');

        $group->put('/{id}', ControlEstadisticosServiciosController::class . ':updateControlEstadisticosServicios')
            ->setName('control-estadisticos-servicios.update');

        $group->delete('/{id}', ControlEstadisticosServiciosController::class . ':deleteControlEstadisticosServicios')
            ->setName('control-estadisticos-servicios.delete');
    })->add(AuthMiddleware::class); 
};
