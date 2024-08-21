<?php

namespace App\Middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Slim\Psr7\Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthMiddleware
{
    private $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $authHeader = $request->getHeader('Authorization')[0] ?? '';

        if (!$authHeader) {
            return $this->unauthorizedResponse('Authorization token not provided');
        }

        $token = str_replace('Bearer ', '', $authHeader);

        if (count(explode('.', $token)) !== 3) {
            return $this->badRequestResponse('Invalid token structure');
        }

        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            $request = $request->withAttribute('user_id', $decoded->sub);
            return $handler->handle($request);
        } catch (\Firebase\JWT\ExpiredException $e) {
            return $this->unauthorizedResponse('Token has expired');
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return $this->unauthorizedResponse('Invalid token signature');
        } catch (\Exception $e) {
            return $this->unauthorizedResponse('Invalid token');
        }
    }

    private function unauthorizedResponse(string $message): Response
    {
        $response = new Response();
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }

    private function badRequestResponse(string $message): Response
    {
        $response = new Response();
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }
}

?>