<?php

namespace App\Middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Psr7\Response;

class AuthMiddleware
{
    private $container;
    private $logger;

    public function __construct(ContainerInterface $container, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->logger = $logger;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $authHeader = $request->getHeader('Authorization')[0] ?? '';

        if (!$authHeader) {
            throw new HttpUnauthorizedException($request, 'El token de autorización es requerido');
        }

        $token = str_replace('Bearer ', '', $authHeader);
        $key = $this->container->get('settings')['jwt']['secret'];

        if (count(explode('.', $token)) !== 3) {
            return $this->badRequestResponse('Invalid token structure');
        }

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            $request = $request->withAttribute('user_id', $decoded->sub);
            return $handler->handle($request);
        } catch (\Firebase\JWT\ExpiredException $e) {
            $this->logger->warning('Token JWT expirado: ' . $e->getMessage());
            return $this->unauthorizedResponse($request, 'Token JWT expirado');
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            $this->logger->warning('Firma del token JWT inválida: ' . $e->getMessage());
            return $this->unauthorizedResponse($request, 'Firma del token JWT inválida');
        } catch (HttpUnauthorizedException $e) {
            return $this->unauthorizedResponse($request, $e->getMessage());
        } catch (\Throwable $e) {
            $this->logger->error('Error inesperado al validar el token JWT: ' . $e->getMessage());
            return $this->unauthorizedResponse($request, 'Error al procesar el token JWT');
        }
    }

    /**
     * Generar una respuesta no autorizada.
     *
     * @param Request $request
     * @param string $message
     * @return Response
     */
    private function unauthorizedResponse(Request $request, string $message): Response
    {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }

    private function badRequestResponse(string $message): Response
    {
        $response = new Response();
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }
}
