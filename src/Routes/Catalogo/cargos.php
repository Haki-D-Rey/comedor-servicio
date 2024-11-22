<?php

namespace App\Routes\Catalogo;

use App\Controllers\Catalogo\CargosController;
use App\Middlewares\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('/cargos', function (RouteCollectorProxy $group) {
        // Rutas estáticas
        $group->get('/all', CargosController::class . ':getAllCargos')->setName('cargos.list_all');
    
        // Rutas dinámicas
        $group->get('/{id}', CargosController::class . ':getCargoById')->setName('cargos.view');
        $group->post('/', CargosController::class . ':createCargo')->setName('cargos.create');
        $group->put('/{id}', CargosController::class . ':updateCargo')->setName('cargos.update');
        $group->delete('/{id}', CargosController::class . ':deleteCargo')->setName('cargos.delete');
    })->add(AuthMiddleware::class); 

};
