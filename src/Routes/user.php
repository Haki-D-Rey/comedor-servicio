<?php

use Slim\App;
use App\Controllers\UsuarioController;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    //Rutas estaticas
    $app->get('/user/all', UsuarioController::class . ':getTestUser');

    $app->group('/user', function (RouteCollectorProxy $group) {
        //Rutas dinamicas
        $group->get('/{id}', UsuarioController::class . ':getUser')->setName('');
        $group->post('/', UsuarioController::class . ':createUser');
        $group->put('/{id}', UsuarioController::class . ':updateUser');
        $group->delete('/{id}', UsuarioController::class . ':deleteUser');
    }); 
};
