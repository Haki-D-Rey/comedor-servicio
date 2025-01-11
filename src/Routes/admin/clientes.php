<?php

namespace App\Routes\Admin;

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\Publico\ClientesController;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\AuthorizationMiddleware;

return function (RouteCollectorProxy $admin) {
    // Define las rutas bajo /admin/clientes
    $admin->group('/admin/clientes', function (RouteCollectorProxy $clientes) {
        // Ruta para obtener todos los clientes
        $clientes->get('/validate-form', ClientesController::class . ':getValidateFormById')->setName('clientes.validate_form');
        $clientes->get('/', ClientesController::class . ':index')
            ->setName('admin_clientes.index')
            ->setArgument('permission', 'admin_clientes_view')
            ->add(AuthorizationMiddleware::class);

        $clientes->get('/{id}', ClientesController::class . ':getClienteById')->setName('clientes.view');
        $clientes->post('/', ClientesController::class . ':createCliente')->setName('clientes.create');
        $clientes->put('/{id}', ClientesController::class . ':updateCliente')->setName('clientes.update');
        $clientes->delete('/{id}', ClientesController::class . ':deleteCliente')->setName('clientes.delete');
    })->add(AuthMiddleware::class);
};
