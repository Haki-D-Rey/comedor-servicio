<?php

namespace App\Middlewares;

use App\Entity\Usuario;
use App\Services\AuthServices;
use App\Services\Seguridad\AuthorizationService;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;

class AuthorizationMiddleware
{
    private AuthorizationService $authorizationService;
    private EntityManager $entityManager;

    public function __construct(AuthorizationService $authorizationService, EntityManager $entityManager)
    {
        $this->authorizationService = $authorizationService;
        $this->entityManager = $entityManager;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $route = RouteContext::fromRequest($request)->getRoute();
        $requiredPermission = $route->getArgument('permission');

        $usuario = $this->getLoggedUser();

        if (!$this->authorizationService->canAccess($usuario, $requiredPermission)) {
            $response = new \Slim\Psr7\Response();
            // $this->authServices->postLogout($request);
            // return $response->withStatus(403, 'Access Denied');
            return $response
                ->withHeader('Location', $_SESSION['previous_url'])  // Redirigir a /formulario
                ->withStatus(302);
        }

        return $handler->handle($request);
    }

    private function getLoggedUser(): Usuario
    {
        if (isset($_SESSION['user_id'])) {
            $usuarioId = $_SESSION['user_id'];
            $usuario = $this->entityManager->getRepository(Usuario::class)->find($usuarioId);

            if (!$usuario) {
                throw new \Exception('Usuario no encontrado');
            }

            return $usuario;
        }

        throw new \Exception('Usuario no autenticado');
    }
}
