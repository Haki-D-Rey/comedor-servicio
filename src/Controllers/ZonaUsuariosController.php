<?php

namespace App\Controllers;

use App\DTO\ZonaUsuarioDTO;
use App\Services\ZonaUsuariosServices;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ZonaUsuariosController
{
    private $zonaUsuariosServices;

    public function __construct(ZonaUsuariosServices $zonaUsuariosServices)
    {
        $this->zonaUsuariosServices = $zonaUsuariosServices;
    }

    public function getAllZonaUsuarios(Request $request, Response $response): Response
    {
        try {
            $resultado = $this->zonaUsuariosServices->getAllZonaUsuarios();
            $response->getBody()->write(json_encode($resultado));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getRelationalZonaUsuarioById(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        try {
            $resultado = $this->zonaUsuariosServices->getRelationalZonaUsuarioById($id);
            $response->getBody()->write(json_encode($resultado));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getZonaUsuarioById(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            $resultado = $this->zonaUsuariosServices->getZonaUsuarioById($id);

            if ($resultado === null) {
                $response->getBody()->write(json_encode(['estado' => false, 'message' => 'Lista de Zona Relacionado a los usuarios no encontrado']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode($resultado));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function createZonaUsuario(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);

        try {
            $zonaUsuarioDTO = new ZonaUsuarioDTO(
                null,
                $data['id_zona'],
                $data['id_usuario'],
                $data['codigo_interno'],
                new \DateTime(),
                null,
                $data['estado'] ?? true
            );

            $this->zonaUsuariosServices->createZonaUsuario($zonaUsuarioDTO);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Zona Relacionado al Usuario creado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function updateZonaUsuario(Request $request, Response $response, array $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $id = (int) $args['id'];

        try {
            $zonaUsuarioDTO = new ZonaUsuarioDTO(
                $id,
                $data['id_zona'],
                $data['id_usuario'],
                $data['codigo_interno'],
                $data['fecha_creacion'] ?? new \DateTime(),
                $data['fecha_modificacion'] ?? new \DateTime(),
                $data['estado'] ?? true
            );

            $this->zonaUsuariosServices->updateZonaUsuario($id, $zonaUsuarioDTO);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Zona Relacionada al Usuario actualizado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function deleteZonaUsuario(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        try {
            $this->zonaUsuariosServices->deleteZonaUsuario($id);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Zona Relacionada al Usuario eliminado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
