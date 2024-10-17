<?php

use Slim\App;
use App\Controllers\DetalleZonaServicioHorarioController;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('/detalle-zona-servicio-horario', function (RouteCollectorProxy $group) {
        // Rutas estáticas
        $group->get('/all', DetalleZonaServicioHorarioController::class . ':getAllDetalleZonasServicioHorario')
            ->setName('detalle-zona-servicio-horario.list_all');

        $group->get('/getByZonaUsuarioDetalleServicio/{id}', DetalleZonaServicioHorarioController::class . ':getAllDetalleZonaServicioHorarioByZonaUsuario')
            ->setName('detalle-zona-servicio-horario.get_zona_usuario_detalles');

        $group->get('/getByIdZonaUsuarioDetalleServicio/{id}', DetalleZonaServicioHorarioController::class . ':getAllDetalleZonaServicioHorarioByIdZonaUsuario')
            ->setName('detalle-zona-servicio-horario.get_id_zona_usuario_detalles');

        // Rutas dinámicas
        $group->get('/{id}', DetalleZonaServicioHorarioController::class . ':getDetalleZonasServicioHorarioById')
            ->setName('detalle-zona-servicio-horario.get_id');

        $group->post('/', DetalleZonaServicioHorarioController::class . ':createDetalleZonasServicioHorario')
            ->setName('detalle-zona-servicio-horario.create');

        $group->put('/{id}', DetalleZonaServicioHorarioController::class . ':updateDetalleZonasServicioHorario')
            ->setName('detalle-zona-servicio-horario.update');

        $group->delete('/{id}', DetalleZonaServicioHorarioController::class . ':deleteDetalleZonasServicioHorario')
            ->setName('detalle-zona-servicio-horario.delete');
    });
};
