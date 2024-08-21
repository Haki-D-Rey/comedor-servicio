<?php

use App\Controllers\AuthController;
use App\Middlewares\AuthMiddleware;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

// use App\Middlewares\AuthMiddleware;

return function (App $app) {
    // Obtener el contenedor de dependencias
    $container = $app->getContainer();

    // Obtener instancias de los modelos
    $userModel = $container->get('userModel');
    $revokedTokensModel = $container->get('revokedTokensModel');

    $authController = new AuthController($userModel, $revokedTokensModel);

    // Crear una instancia del middleware de autorización
    $authMiddleware = new AuthMiddleware($container->get('settings')['jwt']['secret']);

    $app->group('/auth', function (RouteCollectorProxy $group) use ($authController,  $authMiddleware) {


        $group->get('/', function (Request $request,Response $response) use ($authController) {
            // $response = new \Slim\Psr7\Response();
            $response->getBody()->write('Hola bienvenido al modo administrador');
            return $response->withHeader('Content-Type', 'application/json');
        })->add($authMiddleware);

        $group->post('/generate-token', function (Request $request,Response $response) use ($authController) {
            $token = $authController->generateToken();
            $time = $authController -> getTimeRemaining($token);
            $response->getBody()->write(json_encode(['token' => $token, 'Expire' => $time ]));
            return $response->withHeader('Content-Type', 'application/json');
        });

        // Ruta para el login
        $group->post('/login', function (Request $request,Response $response) use ($authController) {
            return $authController->login($request, $response);
        });

        // Ruta para el logout
        $group->post('/logout', function (Request $request,Response $response) use ($authController) {
            return $authController->logout($request, $response);
        });

        $group->get('/protected', function (Request $request,Response $response) {
            $response->getBody()->write("This is a protected route.");
            return $response;
        })->add(function (Request $request, RequestHandlerInterface $handler) use ($authController) {
            return $authController->validateToken($request, $handler);
        }); 
    });
};
