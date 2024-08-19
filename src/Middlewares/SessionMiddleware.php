<?php

namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class SessionMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = new Response();
        $sessionLastActivity = $_SESSION['last_activity'] ?? null;
        $timeout = 1800; // 30 minutos de inactividad permitidos

        if ($sessionLastActivity && (time() - $sessionLastActivity) > $timeout) {
            session_unset();
            session_destroy();
            $response->getBody()->write('Session has expired due to inactivity');
            return $response->withStatus(401);
        }

        $_SESSION['last_activity'] = time(); // Actualizar última actividad

        return $handler->handle($request);
    }
}
