<?php
/*
namespace App\Controllers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthController
{
    private $userModel;
    private $revokedTokensModel;
    private $key = "haki12345";

    public function __construct($userModel, $revokedTokensModel)
    {
        $this->userModel = $userModel;
        $this->revokedTokensModel = $revokedTokensModel;
    }

    public function login(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        $user = $this->userModel->findByUsername($username);

        if (!$user || !$this->userModel->verifyPassword($password, $user->password)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid credentials']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $payload = [
            'iss' => 'your-domain.com',
            'aud' => 'your-domain.com',
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + (60 * 60),
            'sub' => $user->id,
        ];

        $token = JWT::encode($payload, $this->key, 'HS256');

        $response->getBody()->write(json_encode(['token' => $token]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function logout(Request $request, Response $response)
    {
        $authHeader = $request->getHeader('Authorization')[0] ?? '';
        $token = str_replace('Bearer ', '', $authHeader);

        if ($token) {
            $this->revokedTokensModel->addToken($token);
        }

        session_unset();
        session_destroy();

        $response->getBody()->write(json_encode(['message' => 'Successfully logged out']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function validateToken(Request $request, RequestHandler $handler): Response
    {
        $authHeader = $request->getHeader('Authorization')[0] ?? '';

        if (!$authHeader) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Authorization token not provided']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $token = str_replace('Bearer ', '', $authHeader);

        if ($this->revokedTokensModel->isTokenRevoked($token)) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Token has been invalidated']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            $request = $request->withAttribute('user_id', $decoded->sub);

            // Continúa con el siguiente middleware o controlador
            return $handler->handle($request);
        } catch (\Exception $e) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Invalid token']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    }

    public function generateToken()
    {
        $key = $this->key; // Clave para firmar el token
        $payload = [
            'sub' => 1,
            'iat' => time(),
            'exp' => time() + 3600
        ];

        return JWT::encode($payload, $key, 'HS256');
    }

    public function verifyToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            return [
                'valid' => true,
                'data' => $decoded
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function getTimeRemaining($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            $currentTime = time();
            $expirationTime = $decoded->exp;

            if ($currentTime >= $expirationTime) {
                return [
                    'hours' => 0,
                    'minutes' => 0,
                    'seconds' => 0
                ];
            }

            $timeRemaining = $expirationTime - $currentTime;
            $hours = floor($timeRemaining / 3600);
            $minutes = floor(($timeRemaining % 3600) / 60);
            $seconds = $timeRemaining % 60;

            return [
                'hours' => $hours,
                'minutes' => $minutes,
                'seconds' => $seconds
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}*/
