<?php

namespace App\Routes\Publico;

use App\Controllers\Publico\DetalleZonaServicioHorarioClienteFacturacionController;
use App\Middlewares\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('/detalle-zona-servicio-horario-cliente-identificacion-facturacion', function (RouteCollectorProxy $group) {
        // Rutas estáticas
        $group->get('/all', DetalleZonaServicioHorarioClienteFacturacionController ::class . ':getAllDetalleZonaServicioHorarioClienteFacturacion')->setName('detalle_zona_servicio_horario_cliente_identificacion_facturacion.list_all');
    
        // Rutas dinámicas
        $group->get('/{id}', DetalleZonaServicioHorarioClienteFacturacionController ::class . ':getDetalleZonaServicioHorarioClienteFacturacionById')->setName('detalle_zona_servicio_horario_cliente_identificacion_facturacion.view');
        $group->post('/', DetalleZonaServicioHorarioClienteFacturacionController ::class . ':createDetalleZonaServicioHorarioClienteFacturacion')->setName('detalle_zona_servicio_horario_cliente_identificacion_facturacion.create');
        $group->put('/{id}', DetalleZonaServicioHorarioClienteFacturacionController ::class . ':updateDetalleZonaServicioHorarioClienteFacturacion')->setName('detalle_zona_servicio_horario_cliente_identificacion_facturacion.update');
        $group->delete('/{id}', DetalleZonaServicioHorarioClienteFacturacionController ::class . ':deleteDetalleZonaServicioHorarioClienteFacturacion')->setName('detalle_zona_servicio_horario_cliente_identificacion_facturacion.delete');
    })->add(AuthMiddleware::class); 
};
