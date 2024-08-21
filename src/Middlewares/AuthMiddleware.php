<?php

namespace App\Middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthMiddleware
{
    private $key;
    // private $revokedTokensModel;

    public function __construct($key)
    {
        $this->key = $key;
        // $this->revokedTokensModel = $revokedTokensModel;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $authHeader = $request->getHeader('Authorization')[0] ?? '';

        if (!$authHeader) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Authorization token not provided']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $token = str_replace('Bearer ', '', $authHeader);

        if (count(explode('.', $token)) !== 3) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Invalid token structure']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // if ($this->revokedTokensModel->isTokenRevoked($token)) {
        //     $response = new Response();
        //     $response->getBody()->write(json_encode(['error' => 'Token has been invalidated']));
        //     return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        // }

        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            $request = $request->withAttribute('user_id', $decoded->sub);
            return $handler->handle($request);
        } catch (\Firebase\JWT\ExpiredException $e) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Token has expired']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Invalid token signature']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Invalid token']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    }
}
