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
        $usuarios = $this->usuarioService->getAllUsers();
        $response->getBody()->write(json_encode($usuarios));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getUsuarioById(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $usuario = $this->usuarioService->getUserById($id);
        
        if ($usuario === null) {
            $response->getBody()->write(json_encode(['message' => 'Usuario no encontrado']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode($usuario));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function createUsuario(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);

        $usuarioDTO = new UsuarioDTO(
            $data['id'] ?? null,
            $data['nombreUsuario'],
            $data['contrasenia'],
            $data['nombres'],
            $data['apellidos'],
            $data['correo'],
            new \DateTime($data['fechaCreacion']),
            isset($data['fechaModificacion']) ? new \DateTime($data['fechaModificacion']) : null,
            $data['estado']
        );

        $created = $this->usuarioService->createUser($usuarioDTO);
        if ($created) {
            $response->getBody()->write(json_encode(['message' => 'Usuario creado']));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['message' => 'Error al crear usuario']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    public function updateUsuario(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $data = json_decode($request->getBody()->getContents(), true);

        $usuarioDTO = new UsuarioDTO(
            $id,
            $data['nombreUsuario'],
            $data['contrasenia'],
            $data['nombres'],
            $data['apellidos'],
            $data['correo'],
            isset($data['fechaCreacion']) ? new \DateTime($data['fechaCreacion']) : null,
            isset($data['fechaModificacion']) ? new \DateTime($data['fechaModificacion']) : null,
            $data['estado']
        );

        $updated = $this->usuarioService->updateUser($id, $usuarioDTO);
        if ($updated) {
            $response->getBody()->write(json_encode(['message' => 'Usuario actualizado']));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['message' => 'Error al actualizar usuario']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    public function deleteUsuario(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $deleted = $this->usuarioService->deleteUser($id);
        if ($deleted) {
            $response->getBody()->write(json_encode(['message' => 'Usuario eliminado']));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['message' => 'Error al eliminar usuario']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }
}
