<?php

namespace App\Controllers;

use App\DTO\ZonaDTO;
use App\Services\ZonaServices;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ZonaController
{
    private $zonaServices;

    public function __construct(ZonaServices $zonaServices)
    {
        $this->zonaServices = $zonaServices;
    }

    public function getAllZona(Request $request, Response $response): Response
    {
        try {
            $sistemas = $this->zonaServices->getAllZona();
            $response->getBody()->write(json_encode($sistemas));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getZonaById(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            $sistemas = $this->zonaServices->getZonaById($id);

            if ($sistemas === null) {
                $response->getBody()->write(json_encode(['estado' => false, 'message' => 'Lista de Tipos de Servicios no encontrado']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode($sistemas));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function createZona(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);

        try {
            $zonaDTO = new ZonaDTO(
                null,
                $data['nombre'],
                $data['descripcion'],
                $data['codigo_interno'],
                new \DateTime(),
                null,
                $data['estado'] ?? true
            );

            $this->zonaServices->createZona($zonaDTO);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Tipo de Servicio creado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function updateZona(Request $request, Response $response, array $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $id = (int) $args['id'];

        try {
            $zonaDTO = new ZonaDTO(
                $id,
                $data['nombre'],
                $data['descripcion'],
                $data['codigo_interno'],
                $data['fecha_creacion'] ?? new \DateTime(),
                $data['fecha_modificacion'] ?? new \DateTime(),
                $data['estado'] ?? true
            );

            $this->zonaServices->updateZona($id, $zonaDTO);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Sistema actualizado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function deleteZona(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        try {
            $this->zonaServices->deleteZona($id);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Sistema eliminado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
