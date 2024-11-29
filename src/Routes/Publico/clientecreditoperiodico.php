<?php

namespace App\Routes\Publico;

use App\Controllers\Publico\ClientesCreditoPeriodicoController;
use App\Middlewares\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;


return function (App $app) {

    $app->group('/clientes-credito-periodico', function (RouteCollectorProxy $group) {

        $group->get('/all', ClientesCreditoPeriodicoController::class . ':getAllClienteCreditoPeriodico')->setname('clientes_credito_periodico.list_all');

        $group->get('/{id}', ClientesCreditoPeriodicoController::class . ':getClienteCreditoPeriodicoById')->setname('clientes_credito_periodico.view');
        $group->post('/', ClientesCreditoPeriodicoController::class . ':createClienteCreditoPeriodico')->setname('clientes_credito_periodico.create');
        $group->put('/{id}', ClientesCreditoPeriodicoController::class . ':updateClienteCreditoPeriodico')->setname('clientes_credito_periodico.update');
        $group->delete('/{id}', ClientesCreditoPeriodicoController::class . ':deleteClienteCreditoPeriodico')->setname('clientes_credito_periodico.delete');
    })->add(AuthMiddleware::class);
};
