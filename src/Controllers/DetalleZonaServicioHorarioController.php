<?php

namespace App\Controllers;

use App\DTO\DetalleZonasServicioHorarioDTO;
use App\Services\DetalleZonaServicioHorarioServices;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DetalleZonaServicioHorarioController
{
    private $detalleZonaServicioHorarioServices;

    public function __construct(DetalleZonaServicioHorarioServices $detalleZonaServicioHorarioServices)
    {
        $this->detalleZonaServicioHorarioServices = $detalleZonaServicioHorarioServices;
    }

    public function getAllDetalleZonasServicioHorario(Request $request, Response $response): Response
    {
        try {
            $sistemas = $this->detalleZonaServicioHorarioServices->getAllDetalleZonasServicioHorario();
            $response->getBody()->write(json_encode($sistemas));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getAllDetalleZonaServicioHorarioByZonaUsuario(Request $request, Response $response, array $args): Response
    {
        $id = (string) $args['id'];
        try {
            $sistemas = $this->detalleZonaServicioHorarioServices->getAllDetalleZonaServicioHorarioByZonaUsuario($id);
            $response->getBody()->write(json_encode($sistemas));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getAllDetalleZonaServicioHorarioByIdZonaUsuario(Request $request, Response $response, array $args): Response
    {
        $id = (string) $args['id'];
        try {
            $sistemas = $this->detalleZonaServicioHorarioServices->getAllDetalleZonaServicioHorarioByIdZonaUsuario($id);
            $response->getBody()->write(json_encode($sistemas));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getDetalleZonasServicioHorarioById(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            $sistemas = $this->detalleZonaServicioHorarioServices->getDetalleZonasServicioHorarioById($id);

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

    public function createDetalleZonasServicioHorario(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);

        try {

            $detallesZonaServicioHorarioDTOs = [];
            foreach ($data as $detalleData) {

                $detalleDTO = new DetalleZonasServicioHorarioDTO(
                    null,
                    $detalleData['idServiciosProductosDetalles'],
                    $detalleData['idHorario'],
                    $detalleData['idZonaUsuario'],
                    $detalleData['nombre'],
                    $detalleData['descripcion'],
                    $detalleData['codigo_interno'],
                    new \DateTime(),
                    null,
                    $detalleData['estado'] ?? true
                );

                $detallesZonaServicioHorarioDTOs[] = $detalleDTO;
            }
            $this->detalleZonaServicioHorarioServices->createDetalleZonasServicioHorario($detallesZonaServicioHorarioDTOs);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Tipo de Servicio creado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function updateDetalleZonasServicioHorario(Request $request, Response $response, array $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $id = (int) $args['id'];

        try {
            $tipoServiciosDTO = new DetalleZonasServicioHorarioDTO(
                $id,
                $data['idServiciosProductosDetalles'],
                $data['idHorario'],
                $data['idZonaUsuario'],
                $data['nombre'],
                $data['descripcion'],
                $data['codigo_interno'],
                $data['fecha_creacion'] ?? new \DateTime(),
                $data['fecha_modificacion'] ?? new \DateTime(),
                $data['estado'] ?? true
            );

            $this->detalleZonaServicioHorarioServices->updateDetalleZonasServicioHorario($id, $tipoServiciosDTO);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Sistema actualizado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function deleteDetalleZonasServicioHorario(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        try {
            $this->detalleZonaServicioHorarioServices->deleteDetalleZonasServicioHorario($id);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Sistema eliminado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
