<?php

namespace App\Routes\Publico;

use App\Controllers\Publico\ClientesController;
use App\Middlewares\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('/clientes', function (RouteCollectorProxy $group) {
        // Rutas estáticas
        $group->get('/all', ClientesController ::class . ':getAllClientes')->setName('clientes.list_all');
        $group->get('/busqueda', ClientesController::class . ':getSearchClients')->setName('clientes.get_filter_client');
        $group->get('/clientRelationalIdentificaction', ClientesController::class . ':getClientsRelationalIdentification')->setName('clientes.get_client_relational_identification');
        // Rutas dinámicas
        $group->get('/{id}', ClientesController ::class . ':getClienteById')->setName('clientes.view');
        $group->post('/', ClientesController ::class . ':createCliente')->setName('clientes.create');
        $group->put('/{id}', ClientesController ::class . ':updateCliente')->setName('clientes.update');
        $group->delete('/{id}', ClientesController ::class . ':deleteCliente')->setName('clientes.delete');
    })->add(AuthMiddleware::class); 
};
