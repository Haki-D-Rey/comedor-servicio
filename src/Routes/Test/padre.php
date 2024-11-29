<?php

namespace App\Routes\Publico;

use App\Controllers\Test\PadreController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    // Agregar el prefijo 'padre/' al grupo de rutas
    $app->group('/padre', function (RouteCollectorProxy $group) {
        // Rutas estáticas
        $group->post('/conhijos', PadreController::class . ':agregarPadreConHijos')->setName('padre.list_all');
        
        $group->post('/agregarChildJr', PadreController::class . ':agregarChildJr')->setName('padre.jr');
        // Rutas dinámicas
        $group->post('/hijos', PadreController::class . ':agregarHijos')->setName('padre.view');
        $group->post('/ChildJrExistentes', PadreController::class . ':agregarHijosConChildsExistentes')->setName('padre.ddd');
        $group->post('/busquedahijos', PadreController::class . ':agregarPadreBusquedaHijos')->setName('padre.create');
    });
};
