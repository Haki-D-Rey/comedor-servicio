<?php

namespace App\Routes\Publico;

use App\Controllers\Publico\VentasController;
use App\Middlewares\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('/ventas', function (RouteCollectorProxy $group) {
        // Rutas estáticas
        $group->get('/all', VentasController ::class . ':getAllVentas')->setName('ventas.list_all');
    
        // Rutas dinámicas
        $group->get('/{id}', VentasController ::class . ':getVentaById')->setName('ventas.view');
        $group->post('/', VentasController ::class . ':createVenta')->setName('ventas.create');
        $group->put('/{id}', VentasController ::class . ':updateVenta')->setName('ventas.update');
        $group->delete('/{id}', VentasController ::class . ':deleteVenta')->setName('ventas.delete');
    })->add(AuthMiddleware::class); 
};
