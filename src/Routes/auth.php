<?php

use App\Controllers\AuthController;
use Slim\Psr7\Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;
use App\Middlewares\AuthMiddleware;

return function (App $app) {
    // Obtener el contenedor de dependencias
    $container = $app->getContainer();

    // Obtener instancias de los modelos
    $userModel = $container->get('userModel');
    $revokedTokensModel = $container->get('revokedTokensModel');

    $authController = new AuthController($userModel, $revokedTokensModel);

    // Crear una instancia del middleware de autorización
    $authMiddleware = new AuthMiddleware($container->get('settings')['jwt']['secret']);
    // $authMiddleware = $authController->validateToken($request, $handler);

    $app->group('/auth', function ($group) use ($authController, $authMiddleware) {


        $group->get('/', function ($request, $response) use ($authController) {
            $response = new \Slim\Psr7\Response();
            $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlhdCI6MTcyNDIyNDUwMCwiZXhwIjoxNzI0MjI4MTAwfQ.9dWIkLOuvmPwXTe1L24igZ-9_ezHgo8LIKAZbn9eQsg"; //$authController->generateToken();
            $time = $authController -> getTimeRemaining($token);
            $response->getBody()->write(json_encode(['token' => $token, 'Expire' => $time ]));
            return $response->withHeader('Content-Type', 'application/json');
        })->add($authMiddleware);

        // $group->get('/', function ($request, $response) {
        //     $response->getBody()->write("Welcome to the login user software comedor servicio.");
        //     return $response;
        // })->add($authMiddleware);

        // Ruta para el login
        $group->post('/login', function ($request, $response) use ($authController) {
            return $authController->login($request, $response);
        });

        // Ruta para el logout
        $group->post('/logout', function ($request, $response) use ($authController) {
            return $authController->logout($request, $response);
        });

        $group->get('/protected', function ($request, $response) {
            $response->getBody()->write("This is a protected route.");
            return $response;
        })->add(function (Request $request, RequestHandlerInterface $handler) use ($authController) {
            return $authController->validateToken($request, $handler);
        }); // ->add($authMiddleware); // Aplicar el middleware a esta ruta
    });
};
