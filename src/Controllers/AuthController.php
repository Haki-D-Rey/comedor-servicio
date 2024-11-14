<?php

namespace App\Controllers;

use App\Services\AuthServices;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class AuthController
{
    private $authServices;
    private $container;

    public function __construct(AuthServices $authServices, ContainerInterface $container)
    {
        $this->authServices = $authServices;
        $this->container = $container;
    }

    public function loginview(Request $request, Response $response): Response
    {
        ob_start();

        include __DIR__ . '/../../public/views/client/auth/login.php';

        $viewContent = ob_get_clean();
        $response->getBody()->write($viewContent);

        return $response->withHeader('Content-Type', 'text/html');
    }


    public function login(Request $request, Response $response)
    {
        // Iniciar la sesión
        session_start();
        $data = $request->getParsedBody();

        try {
            $credenciales = [
                "username" => $data["username"],
                "password" => $data["password"]
            ];

            $authData = $this->authServices->postlogin($credenciales);
            $timeExpirationToken = $this->getTimeRemaining($authData['expiracion']);
            $authData['expiracion'] = $timeExpirationToken;

            $_SESSION['jwt_token'] = $authData['token'];
            return $response
                ->withHeader('Location', '/dashboard/')  // Redirigir a /formulario
                ->withStatus(302);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function logout(Request $request, Response $response)
    {
        $this->authServices->postLogout($request);
        $response->getBody()->write(json_encode(['status' => true, 'message' => 'Successfully logged out']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function verifyToken(Request $request, Response $response, array $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        try {

            $resultado = $this->authServices->verifyToken($data, $this->container);
            $response->getBody()->write(json_encode($resultado));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'valido' => false,
                'error' => $e->getMessage(),
                'tiempoExpiracion' => [
                    'horas' => 0,
                    'minutos' => 0,
                    'segundos' => 0
                ]
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }

    public function getTimeRemaining($expirationTime)
    {
        try {
            date_default_timezone_set('America/Guatemala');
            $currentTime = time();

            if ($currentTime >= $expirationTime) {
                return [
                    'horas' => 0,
                    'minutos' => 0,
                    'segundos' => 0
                ];
            }

            $timeRemaining = $expirationTime - $currentTime;
            $hours = floor($timeRemaining / 3600);
            $minutes = floor(($timeRemaining % 3600) / 60);
            $seconds = $timeRemaining % 60;

            return [
                'horas' => $hours,
                'minutos' => $minutes,
                'segundos' => $seconds,
                'time' => $currentTime
            ];
        } catch (\Exception $e) {
            return [
                'horas' => 0,
                'minutos' => 0,
                'segundos' => 0,
                'time' => 0
            ];
        }
    }
}
