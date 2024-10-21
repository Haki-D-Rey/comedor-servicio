<?php


namespace App\Middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Response;

class PreventLoginIfAuthenticatedMiddleware
{
    private $container;
    private $logger;

    public function __construct(ContainerInterface $container,  LoggerInterface $logger)
    {
        $this->container = $container;
        $this->logger = $logger;
    }


    public function __invoke(Request $request, RequestHandler $handler): Response
    {
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
        $response = new Response();
        $key = $this->container->get('settings')['jwt']['secret'];
        $decoded = $token ? JWT::decode($token, new Key($key, 'HS256')) : false;

        // Si no se encontró el token en ningún lugar
        if (!$decoded) {
            return $handler->handle($request);
        }


        try {
            // Obtener la URL almacenada en la cookie 'previous_url', o redirigir al dashboard si no hay una URL previa
            $previousUrl = $_SESSION['previous_url'] ?? '/';
            // session_destroy();
            return $response->withHeader('Location', $previousUrl)->withStatus(302);
        } catch (\Throwable $e) {
            $this->logger->error('Error inesperado al validar el token JWT: ' . $e->getMessage());
            return $this->unauthorizedResponse($request, 'Error al procesar el token JWT');
        }
    }

    private function unauthorizedResponse(Request $request, string $message): Response
    {
        $response = new Response();
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }
}
