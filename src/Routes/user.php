<?php

use Slim\App;
use App\Controllers\UsuarioController;
use App\Controllers\AuthController;
use App\Middlewares\PreventLoginIfAuthenticatedMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->post('/token', AuthController::class . ':verifyToken');

    $app->group('/user', function (RouteCollectorProxy $group) {
        // Rutas estáticas
        $group->get('/all', UsuarioController::class . ':getAllUsuarios');

        // Rutas dinámicas
        $group->get('/{id}', UsuarioController::class . ':getUsuarioById');
        $group->post('/', UsuarioController::class . ':createUsuario');
        $group->put('/{id}', UsuarioController::class . ':updateUsuario');
        $group->delete('/{id}', UsuarioController::class . ':deleteUsuario');
    });

    $app->group('/auth', function (RouteCollectorProxy $group) {
        $group->get('/login', AuthController::class . ':loginview')->add(PreventLoginIfAuthenticatedMiddleware::class);
        $group->post('/login', AuthController::class . ':login')->setName('auth-login');
        $group->post('/logout', AuthController::class . ':logout')->setName('auth-logout');
        $group->post('/register', AuthController::class . ':registerview');
    });
};
