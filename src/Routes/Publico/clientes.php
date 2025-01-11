<?php

namespace App\Routes\Publico;

use App\Controllers\Publico\ClientesController;
use App\Middlewares\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('/clientes', function (RouteCollectorProxy $group) {
        // Rutas estáticas (sin parámetros dinámicos)
        $group->get('/all', ClientesController::class . ':getAllClientes')->setName('clientes.list_all');
        $group->get('/busqueda', ClientesController::class . ':getSearchClients')->setName('clientes.get_filter_client');
        $group->get('/clientRelationalIdentificaction', ClientesController::class . ':getClientsRelationalIdentification')->setName('clientes.get_client_relational_identification');
    })->add(AuthMiddleware::class); // Asegúrate de agregar el middleware de autenticación solo si lo necesitas
};
