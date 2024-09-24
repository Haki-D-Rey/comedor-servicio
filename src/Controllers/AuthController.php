<?php

namespace App\Controllers;

use App\Services\AuthServices;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
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
        $data = $request->getParsedBody();

        try {
            $credenciales = [
                "username" => $data["username"],
                "password" => $data["password"]
            ];

            $authData = $this->authServices->postlogin($credenciales);
            $timeExperitiontoken = $this->getTimeRemaining($authData['expiracion']);
            $authData['expiracion'] = $timeExperitiontoken;
            setcookie(
                'jwt_token',
                $authData['token'],
                [
                    'expires' => $timeExperitiontoken, // Expira en 1 hora
                    'path' => '/',
                    'domain' => 'tu-dominio.com', // Cambia por tu dominio
                    'secure' => true, // Solo en HTTPS
                    'httponly' => true, // Solo accesible por HTTP, no por JavaScript
                    'samesite' => 'Strict' // Protege contra CSRF
                ]
            );
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Inicio Sesion Exitosamente', 'resultado' =>  $authData]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    // public function logout(Request $request, Response $response)
    // {
    //     $authHeader = $request->getHeader('Authorization')[0] ?? '';
    //     $token = str_replace('Bearer ', '', $authHeader);

    //     if ($token) {
    //         $this->revokedTokensModel->addToken($token);
    //     }

    //     session_unset();
    //     session_destroy();

    //     $response->getBody()->write(json_encode(['message' => 'Successfully logged out']));
    //     return $response->withHeader('Content-Type', 'application/json');
    // }

    // public function validateToken(Request $request, RequestHandler $handler): Response
    // {
    //     $authHeader = $request->getHeader('Authorization')[0] ?? '';

    //     if (!$authHeader) {
    //         $response = new \Slim\Psr7\Response();
    //         $response->getBody()->write(json_encode(['error' => 'Authorization token not provided']));
    //         return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    //     }

    //     $token = str_replace('Bearer ', '', $authHeader);

    //     if ($this->revokedTokensModel->isTokenRevoked($token)) {
    //         $response = new \Slim\Psr7\Response();
    //         $response->getBody()->write(json_encode(['error' => 'Token has been invalidated']));
    //         return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    //     }

    //     try {
    //         $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
    //         $request = $request->withAttribute('user_id', $decoded->sub);

    //         // Continúa con el siguiente middleware o controlador
    //         return $handler->handle($request);
    //     } catch (\Exception $e) {
    //         $response = new \Slim\Psr7\Response();
    //         $response->getBody()->write(json_encode(['error' => 'Invalid token']));
    //         return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    //     }
    // }

    // public function generateToken()
    // {
    //     $key = $this->key; // Clave para firmar el token
    //     $payload = [
    //         'sub' => 1,
    //         'iat' => time(),
    //         'exp' => time() + 3600
    //     ];

    //     return JWT::encode($payload, $key, 'HS256');
    // }

    public function verifyToken(Request $request, Response $response, array $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        try {
            $decoded = JWT::decode($data['token'], new Key($this->container->get('settings')['jwt']['secret'], 'HS256'));

            $expirationTime = $this->getTimeRemaining($decoded->exp);

            $response->getBody()->write(json_encode([
                'valido' => true,
                'token' => $data['token'],
                'tiempoExpiracion' => $expirationTime
            ]));
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
            // $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            // $expirationTime = $decoded->exp;
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
                'segundos' => $seconds
            ];
        } catch (\Exception $e) {
            return [
                'horas' => 0,
                'minutos' => 0,
                'segundos' => 0
            ];
        }
    }
}
