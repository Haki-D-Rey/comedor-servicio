<?php

namespace App\Controllers;

use App\Services\AuthServices;
use Slim\Psr7\Response;
use Slim\Psr7\Request;

use PHPMailer\PHPMailer\PHPMailer;
use Psr\Container\ContainerInterface;
use Slim\Routing\RouteParser;

class ApiController
{
    private $container;
    private $authServices;

    public function __construct(ContainerInterface $container, AuthServices $authServices)
    {
        $this->container = $container;
        $this->authServices = $authServices;
    }

    protected PHPMailer $mailServer;

    public function index(Request $request, Response $response): Response
    {
        ob_start();

        // include __DIR__ . '/views/client/inicio.php';
        include __DIR__ . '/../../public/views/client/inicio.php';

        $viewContent = ob_get_clean();
        $response->getBody()->write($viewContent);

        return $response->withHeader('Content-Type', 'text/html');
    }

    public function formulario(Request $request, Response $response): Response
    {
        $routeParser = $this->container->get(RouteParser::class);
        $data = [
            "token" => $_COOKIE['jwt_token']
        ];
        $user_id = $this->authServices->verifyToken($data, $this->container)['user']->sub;
        ob_start();
        // include __DIR__ . '/views/client/inscripcion_control.php';
        include __DIR__ . '/../../public/views/client/formulario/inscripcion_control.php';
        $viewContent = ob_get_clean();
        $response->getBody()->write($viewContent);

        return $response->withHeader('Content-Type', 'text/html');
    }

    public function info(Request $request, Response $response): Response
    {
        ob_start();
        phpinfo();
        $phpinfo = ob_get_clean();

        $response->getBody()->write($phpinfo); // Escribe el contenido en la respuesta
        return $response->withHeader('Content-Type', 'text/html'); // Establece el tipo de contenido como HTML
    }
}
