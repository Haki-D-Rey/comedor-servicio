<?php

namespace App\Routes\Catalogo;

use App\Controllers\Catalogo\TipoUsuariosController;
use App\Middlewares\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('/tipo-usuarios', function (RouteCollectorProxy $group) {
        // Rutas estáticas
        $group->get('/all', TipoUsuariosController::class . ':getAllTipoUsuarios')->setName('tipo_usuarios.list_all');
    
        // Rutas dinámicas
        $group->get('/{id}', TipoUsuariosController::class . ':getTipoUsuarioById')->setName('tipo_usuarios.view');
        $group->post('/', TipoUsuariosController::class . ':createTipoUsuario')->setName('tipo_usuarios.create');
        $group->put('/{id}', TipoUsuariosController::class . ':updateTipoUsuario')->setName('tipo_usuarios.update');
        $group->delete('/{id}', TipoUsuariosController::class . ':deleteTipoUsuario')->setName('tipo_usuarios.delete');
    })->add(AuthMiddleware::class); 

};
