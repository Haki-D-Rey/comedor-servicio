<?php

namespace App\Controllers;

use App\DTO\SistemasDTO;
use App\Services\SistemasServices;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SistemasController
{
    private $sistemasService;

    public function __construct(SistemasServices $sistemasService)
    {
        $this->sistemasService = $sistemasService;
    }

    public function getAllSistemas(Request $request, Response $response): Response
    {
        try {
            $sistemas = $this->sistemasService->getAllSistemas();
            $response->getBody()->write(json_encode($sistemas));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getSistemaById(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            $sistemas = $this->sistemasService->getSistemaById($id);

            if ($sistemas === null) {
                $response->getBody()->write(json_encode(['estado' => false, 'message' => 'Lista de Sistemas no encontrado']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode($sistemas));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function createSistema(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);

        try {
            $sistemasDTO = new SistemasDTO(
                null,
                $data['nombre'],
                $data['descripcion'],
                $data['codigo_interno'],
                new \DateTime(),
                null,
                $data['estado'] ?? true
            );

            $this->sistemasService->createSistema($sistemasDTO);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Sistema creado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function updateSistema(Request $request, Response $response, array $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $id = (int) $args['id'];

        try {
            $sistemasDTO = new SistemasDTO(
                $id,
                $data['nombre'],
                $data['descripcion'],
                $data['codigo_interno'],
                $data['fecha_creacion'] ?? new \DateTime(),
                $data['fecha_modificacion'] ?? new \DateTime(),
                $data['estado'] ?? true
            );

            $this->sistemasService->updateSistema($id, $sistemasDTO);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Sistema actualizado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function deleteSistema(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        try {
            $this->sistemasService->deleteSistema($id);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Sistema eliminado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
