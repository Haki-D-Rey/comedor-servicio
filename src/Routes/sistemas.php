<?php

use Slim\App;
use App\Controllers\SistemasController;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('/sistemas', function (RouteCollectorProxy $group) {
        // Rutas estáticas
        $group->get('/all', SistemasController::class . ':getAllSistemas');

        // Rutas dinámicas
        $group->get('/{id}', SistemasController::class . ':getSistemaById');
        $group->post('/', SistemasController::class . ':createSistema');
        $group->put('/{id}', SistemasController::class . ':updateSistema');
        $group->delete('/{id}', SistemasController::class . ':deleteSistema');
    });

};
