<?php

use Slim\App;
use App\Controllers\ZonaController;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('/zonas', function (RouteCollectorProxy $group) {
        // Rutas estáticas
        $group->get('/all', ZonaController::class . ':getAllZona')->setName('zona.list_all');
    
        // Rutas dinámicas
        $group->get('/{id}', ZonaController::class . ':getZonaById')->setName('zona.view');
        $group->post('/', ZonaController::class . ':createZona')->setName('zona.create');
        $group->put('/{id}', ZonaController::class . ':updateZona')->setName('zona.update');
        $group->delete('/{id}', ZonaController::class . ':deleteZona')->setName('zona.delete');
    });

};
