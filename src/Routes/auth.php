<?php

use App\Controllers\AuthController;
use Slim\App;

return function (App $app) {
    // Obtener el contenedor de dependencias
    $container = $app->getContainer();

    // Obtener instancias de los modelos
    $userModel = $container->get('userModel');
    $revokedTokensModel = $container->get('revokedTokensModel');

    // Crear una instancia del controlador de autenticación
    $authController = new AuthController($userModel, $revokedTokensModel);

    // Ruta para el login
    $app->post('/login', function ($request, $response) use ($authController) {
        return $authController->login($request, $response);
    });

    // Ruta para el logout
    $app->post('/logout', function ($request, $response) use ($authController) {
        return $authController->logout($request, $response);
    });

    // Middleware para validar el token en rutas protegidas
    $app->add(function ($request, $response, $next) use ($authController) {
        return $authController->validateToken($request, $response, $next);
    });

    // Ejemplo de ruta protegida
    $app->get('/protected', function ($request, $response) {
        $response->getBody()->write("This is a protected route.");
        return $response;
    })->add(function ($request, $response, $next) use ($authController) {
        return $authController->validateToken($request, $response, $next);
    });
};
