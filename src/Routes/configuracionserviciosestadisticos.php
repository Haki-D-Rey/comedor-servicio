<?php

use Slim\App;
use App\Controllers\ConfiguracionServiciosEstadisticosController;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('/configuracion-servicios-estadisticos', function (RouteCollectorProxy $group) {
        // Rutas estáticas
        $group->get('/all', ConfiguracionServiciosEstadisticosController::class . ':getAllConfiguracionServiciosEstadisticos')
            ->setName('configuracion-servicios-estadisticos.list_all');

        // Rutas dinámicas
        $group->get('/{id}', ConfiguracionServiciosEstadisticosController::class . ':getConfiguracionServiciosEstadisticosById')
            ->setName('configuracion-servicios-estadisticos.get_id');

        $group->post('/', ConfiguracionServiciosEstadisticosController::class . ':createConfiguracionServiciosEstadisticos')
            ->setName('configuracion-servicios-estadisticos.create');

        $group->put('/{id}', ConfiguracionServiciosEstadisticosController::class . ':updateConfiguracionServiciosEstadisticos')
            ->setName('configuracion-servicios-estadisticos.update');

        $group->delete('/{id}', ConfiguracionServiciosEstadisticosController::class . ':deleteConfiguracionServiciosEstadisticos')
            ->setName('configuracion-servicios-estadisticos.delete');
    });
};
