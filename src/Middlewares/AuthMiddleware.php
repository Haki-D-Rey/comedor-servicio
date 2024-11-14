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
        try {
        session_start();
        $token = $_SESSION['jwt_token'] ?? null;

        // Si no está en la cookie, intentar obtenerlo desde el header 'cookie' o 'authorization'
        if (!$token) {
            $cookiesHeader = $request->getHeader('cookie')[0] ?? null;

            if ($cookiesHeader) {
                // Buscar en las cookies el token jwt_token
                $cookies = explode(';', $cookiesHeader);
                foreach ($cookies as $cookie) {
                    $cookie = trim($cookie); // Eliminar espacios
                    if (strpos($cookie, 'jwt_token=') === 0) {
                        $token = str_replace('jwt_token=', '', $cookie);
                        break;
                    }
                }
            }

            // Si no se encontró en las cookies, intentamos obtenerlo del header Authorization
            if (!$token) {
                $authHeader = $request->getHeader('authorization')[0] ?? null;
                if ($authHeader) {
                    $token = str_replace('Bearer ', '', $authHeader);
                }
            }
        }

        // Si no se encontró el token en ningún lugar
        if (!$token) {

            throw new HttpUnauthorizedException($request, 'El token de autorización es requerido');
        }

        $key = $this->container->get('settings')['jwt']['secret'];

        if (count(explode('.', $token)) !== 3) {
            return $this->badRequestResponse('Invalid token structure');
        }

            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            $request = $request->withAttribute('user_id', $decoded->sub);
            $_SESSION['user_id'] = $decoded->sub;
            $_SESSION['loggedin'] = True;
            // session_destroy();
            return $handler->handle($request);
        } catch (\Firebase\JWT\ExpiredException $e) {
            $_SESSION['loggedin'] = False;
            $this->logger->warning('Token JWT expirado: ' . $e->getMessage());
            return $this->unauthorizedResponse($request, 'Token JWT expirado');
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            $_SESSION['loggedin'] = False;
            $this->logger->warning('Firma del token JWT inválida: ' . $e->getMessage());
            return $this->unauthorizedResponse($request, 'Firma del token JWT inválida');
        } catch (HttpUnauthorizedException $e) {
            $_SESSION['loggedin'] = False;
            return $this->unauthorizedResponse($request, $e->getMessage());
        } catch (\Throwable $e) {
            $_SESSION['loggedin'] = False;
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
        $response->withHeader('Location', '/auth/login')->withStatus(302);
        $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        return $response->withHeader('Location', '/auth/login')->withStatus(302); 
    }

    private function badRequestResponse(string $message): Response
    {
        $response = new Response();
        return  $response->withHeader('Location', '/auth/login')->withStatus(302);
    }
}
