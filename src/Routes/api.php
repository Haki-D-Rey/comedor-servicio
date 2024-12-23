<?php

namespace App\Routes;

use App\Controllers\ApiController;
use App\Middlewares\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    // $app->get('/', ApiController::class . ':index');

    $app->get('/info', ApiController::class . ':info');
    $app->group('/api', function (RouteCollectorProxy $group) {
        $group->get('/all', ApiController::class . ':getAll')->setName('api.all');
        $group->get('/excel', ApiController::class . ':getExcelReporteServiciosAlimentacion')->setName('api.getExcel');
        $group->get('/reporte-eventos', ApiController::class . ':getExcelReportInscripcionesEvent')->setName('api.reportEvents');
        $group->get('/data-eventos', ApiController::class . ':getPlanInscripcionEvents')->setName('api.dataEventos');
        $group->get('/con', ApiController::class . ':getConnection')->setName('api.con');
    });
};
