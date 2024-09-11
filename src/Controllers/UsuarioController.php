<?php

namespace App\Controllers;

use App\DTO\UsuarioDTO;
use App\Services\UsuarioServices;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UsuarioController
{
    private $usuarioService;

    public function __construct(UsuarioServices $usuarioService)
    {
        $this->usuarioService = $usuarioService;
    }

    public function getAllUsuarios(Request $request, Response $response): Response
    {
        try {
            $usuarios = $this->usuarioService->getAllUsers();
            $response->getBody()->write(json_encode($usuarios));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getUsuarioById(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            $usuario = $this->usuarioService->getUserById($id);

            if ($usuario === null) {
                $response->getBody()->write(json_encode(['estado' => false, 'message' => 'Usuario no encontrado']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode($usuario));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function createUsuario(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);

        try {
            $usuarioDTO = new UsuarioDTO(
                null,
                $data['nombreUsuario'],
                $data['contrasenia'],
                $data['nombres'],
                $data['apellidos'],
                $data['correo'],
                new \DateTime(),
                null,
                $data['isAdmin'] ?? 0,
                1
            );

            $this->usuarioService->createUser($usuarioDTO);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Usuario creado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function updateUsuario(Request $request, Response $response, array $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $id = (int) $args['id'];

        try {
            $usuarioDTO = new UsuarioDTO(
                $id,
                $data['nombreUsuario'],
                $data['contrasenia'],
                $data['nombres'],
                $data['apellidos'],
                $data['correo'],
                $data['fecha_creacion'] ?? new \DateTime(),
                $data['fecha_modificacion'] ?? new \DateTime(),
                $data['isadmin'] ?? 0,
                $data['estado'] ?? 1
            );

            $this->usuarioService->updateUser($id, $usuarioDTO);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Usuario actualizado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function deleteUsuario(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        try {
            $this->usuarioService->deleteUser($id);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Usuario eliminado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
