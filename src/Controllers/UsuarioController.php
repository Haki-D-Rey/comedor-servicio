<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\DTO\UsuarioDTO;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class UsuarioController
{
    public $usuarioModel;

    public function __construct(UsuarioModel $usuarioModel)
    {
        $this->usuarioModel = $usuarioModel;
    }

    public function getTestUser(Request $request, Response $response): Response
    {
        // $userId = (int) $args['id'];
        // $usuarioDTO = $this->usuarioModel->getUser($userId);

        // if (!$usuarioDTO) {
        //     return $response->withStatus(404)->getBody()->write('Usuario no encontrado');
        // }
        $const= "hola";

        $response->getBody()->write(json_encode('no existe'));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getUser(Request $request, Response $response, $args): Response
    {
        $userId = (int) $args['id'];
        $usuarioDTO = $this->usuarioModel->getUser($userId);

        if (!$usuarioDTO) {
            return $response->withStatus(404)->getBody()->write('Usuario no encontrado');
        }

        $response->getBody()->write(json_encode($usuarioDTO));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function createUser(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $usuarioDTO = new UsuarioDTO(
            0,
            $data['nombreUsuario'],
            $data['contrasenia'],
            $data['nombres'],
            $data['apellidos'],
            $data['correo'],
            new \DateTime(),
            null,
            $data['estado']
        );

        $this->usuarioModel->createUser($usuarioDTO);
        $response->withStatus(201)->getBody()->write(json_encode('Usuario creado'));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function updateUser(Request $request, Response $response, $args): Response
    {
        $userId = (int) $args['id'];
        $data = json_decode($request->getBody()->getContents(), true);
        $usuarioDTO = new UsuarioDTO(
            $userId,
            $data['nombreUsuario'],
            $data['contrasenia'],
            $data['nombres'],
            $data['apellidos'],
            $data['correo'],
            new \DateTime(),
            null,
            $data['estado']
        );

        if (!$this->usuarioModel->updateUser($userId, $usuarioDTO)) {
            return $response->withStatus(404)->getBody()->write('Usuario no encontrado');
        }

        $response->getBody()->write(json_encode('Usuario actualizado'));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function deleteUser(Request $request, Response $response, $args): Response
    {
        $userId = (int) $args['id'];
        if (!$this->usuarioModel->deleteUser($userId)) {
            return $response->withStatus(404)->getBody()->write('Usuario no encontrado');
        }

        $response->getBody()->write(json_encode('Usuario eliminado'));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
