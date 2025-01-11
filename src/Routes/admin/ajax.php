<?php

namespace App\Routes\Admin;

use App\Controllers\Publico\AjaxController;
use App\Middlewares\AuthMiddleware;
use Slim\Routing\RouteCollectorProxy;
use App\Middlewares\AuthorizationMiddleware;

return function (RouteCollectorProxy $admin) {
    // Define las rutas bajo /admin/clientes
    $admin->group('/admin/ajax', function (RouteCollectorProxy $ajax) {
        // Ruta para obtener todos los clientes
        $ajax->post('/server-side-query', AjaxController::class . ':postAjaxServerSide')
            ->setName('admin_ajax.server-side-query')
            ->setArgument('permission', 'admin_ajax_server_side_query')
            ->add(AuthorizationMiddleware::class);
    })->add(AuthMiddleware::class);
};
