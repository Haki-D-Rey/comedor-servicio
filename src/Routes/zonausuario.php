<?php

use Slim\App;
use App\Controllers\ZonaUsuariosController;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('/zona-usuarios', function (RouteCollectorProxy $group) {
        // Rutas estáticas
        $group->get('/all', ZonaUsuariosController::class . ':getAllZonaUsuarios')->setName('zona_usuarios.list_all');
        $group->get('/get-relation-zona-user-all/{id}', ZonaUsuariosController::class . ':getRelationalZonaUsuarioById')->setName('zona_usuarios.list_relational_all');
    
        // Rutas dinámicas
        $group->get('/{id}', ZonaUsuariosController::class . ':getZonaUsuarioById')->setName('zona_usuarios.view');
        $group->post('/', ZonaUsuariosController::class . ':createZonaUsuario')->setName('zona_usuarios.create');
        $group->put('/{id}', ZonaUsuariosController::class . ':updateZonaUsuario')->setName('zona_usuarios.update');
        $group->delete('/{id}', ZonaUsuariosController::class . ':deleteZonaUsuario')->setName('zona_usuarios.delete');
    });

};
