<?php

namespace App\Middlewares;

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

namespace App\Middlewares;

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class SaveRefererMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        session_start();
        // Obtener el protocolo actual (HTTP o HTTPS)
        $protocol = (!empty($request->getServerParams()['HTTPS']) && $request->getServerParams()['HTTPS'] !== 'off' || $request->getServerParams()['SERVER_PORT'] == 443) ? 'https://' : 'http://';

        // Obtener el host
        $host = $request->getServerParams()['HTTP_HOST'];

        // Obtener el valor de HTTP_REFERER
        $referer = $request->getServerParams()['HTTP_REFERER'] ?? null;

        if ($referer && strpos($referer, '/auth/login') === false) {
            $uri = str_replace($protocol . $host, '', $referer);

            $_SESSION['previous_url'] = $uri;
        }

        return $handler->handle($request);
    }
}
