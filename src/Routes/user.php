<?php

use Slim\App;
use App\Controllers\UsuarioController;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
 
    $app->group('/user', function (RouteCollectorProxy $group) {

        //Rutas estaticas
        $group->get('/all', UsuarioController::class . ':getAllUsuarios');

        //Rutas dinamicas
        $group->get('/{id}', UsuarioController::class . ':getUsuarioById')->setName('');
        $group->post('/', UsuarioController::class . ':createUsuario');
        $group->put('/{id}', UsuarioController::class . ':updateUsuario');
        $group->delete('/{id}', UsuarioController::class . ':deleteUsuario');
    }); 
};
