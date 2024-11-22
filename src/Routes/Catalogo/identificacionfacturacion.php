<?php

namespace App\Routes\Catalogo;

use App\Controllers\Catalogo\IdentificacionFacturacionController;
use App\Middlewares\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('/identificacion-facturacion', function (RouteCollectorProxy $group) {
        // Rutas estáticas
        $group->get('/all', IdentificacionFacturacionController ::class . ':getAllIdentificacionFacturacion')->setName('identificacion_facturacion.list_all');
    
        // Rutas dinámicas
        $group->get('/{id}', IdentificacionFacturacionController ::class . ':getIdentificacionFacturacionById')->setName('identificacion_facturacion.view');
        $group->post('/', IdentificacionFacturacionController ::class . ':createIdentificacionFacturacion')->setName('identificacion_facturacion.create');
        $group->put('/{id}', IdentificacionFacturacionController ::class . ':updateIdentificacionFacturacion')->setName('identificacion_facturacion.update');
        $group->delete('/{id}', IdentificacionFacturacionController ::class . ':deleteIdentificacionFacturacion')->setName('identificacion_facturacion.delete');
    })->add(AuthMiddleware::class); 
};
