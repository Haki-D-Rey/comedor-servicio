<?php

namespace App\Middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = new Response();

        $authHeader = $request->getHeader('Authorization');
        if (!$authHeader) {
            $response->getBody()->write('Authorization token not provided');
            return $response->withStatus(401);
        }

        $token = str_replace('Bearer ', '', $authHeader[0]);

        try {
            $key = "secret_key"; // Usa la misma clave que para generar el token
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            $request = $request->withAttribute('user_id', $decoded->sub);

            return $handler->handle($request);

        } catch (\Exception $e) {
            $response->getBody()->write('Invalid token: ' . $e->getMessage());
            return $response->withStatus(401);
        }
    }
}
