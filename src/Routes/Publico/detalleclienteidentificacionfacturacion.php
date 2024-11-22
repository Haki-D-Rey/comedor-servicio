<?php

namespace App\Routes\Publico;

use App\Controllers\Publico\DetalleClienteIdentificacionFacturacionController;
use App\Middlewares\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('/detalle-cliente-identificacion-facturacion', function (RouteCollectorProxy $group) {
        // Rutas estáticas
        $group->get('/all', DetalleClienteIdentificacionFacturacionController ::class . ':getAllDetalleClienteIdentificacionFacturacion')->setName('detalle_cliente_identificacion_facturacion.list_all');
    
        // Rutas dinámicas
        $group->get('/{id}', DetalleClienteIdentificacionFacturacionController ::class . ':getDetalleClienteIdentificacionFacturacionById')->setName('detalle_cliente_identificacion_facturacion.view');
        $group->post('/', DetalleClienteIdentificacionFacturacionController ::class . ':createDetalleClienteIdentificacionFacturacion')->setName('detalle_cliente_identificacion_facturacion.create');
        $group->put('/{id}', DetalleClienteIdentificacionFacturacionController ::class . ':updateDetalleClienteIdentificacionFacturacion')->setName('detalle_cliente_identificacion_facturacion.update');
        $group->delete('/{id}', DetalleClienteIdentificacionFacturacionController ::class . ':deleteDetalleClienteIdentificacionFacturacion')->setName('detalle_cliente_identificacion_facturacion.delete');
    })->add(AuthMiddleware::class); 
};
