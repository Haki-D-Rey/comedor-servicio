<?php

namespace App\Controllers;

use App\DTO\Publico\ControlEstadisticosServiciosDTO;
use App\Entity\Publico\ControlEstadisticosServicios;
use App\Services\ControlEstadisticosServiciosServices;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ControlEstadisticosServiciosController
{
    private ControlEstadisticosServiciosServices $controlEstadisticosServiciosServices;

    public function __construct(ControlEstadisticosServiciosServices $controlEstadisticosServiciosServices)
    {
        $this->controlEstadisticosServiciosServices = $controlEstadisticosServiciosServices;
    }

    public function getAllControlEstadisticosServicios(Request $request, Response $response): Response
    {
        try {
            $servicios = $this->controlEstadisticosServiciosServices->getAllControlEstadisticosServicios();
            $response->getBody()->write(json_encode($servicios));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getControlEstadisticosServiciosById(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            $servicio = $this->controlEstadisticosServiciosServices->getControlEstadisticosServiciosById($id);

            if ($servicio === null) {
                $response->getBody()->write(json_encode(['estado' => false, 'message' => 'Control de Estadísticos de Servicios no encontrado']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode($servicio));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getControlEstadisticosServiciosByIdForDate(Request $request, Response $response, array $args): Response
    {
        try {
            $date = (string) $args['date'];
            $servicio = $this->controlEstadisticosServiciosServices->getControlEstadisticosServiciosByIdForDate($date);

            if ($servicio === null) {
                $response->getBody()->write(json_encode(['estado' => false, 'message' => 'Control de Estadísticos de Servicios no encontrado']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode($servicio));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function createFormControlEstadisticosServicios(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        try {
            $configuracionServiciosEstadisticos = [
                "fecha_corte" => $data['fecha_corte'],
                "json_configuracion" => $data['json_configuracion']
            ];

            $this->controlEstadisticosServiciosServices->createFormControlEstadisticosServicios($configuracionServiciosEstadisticos);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'El Formulario de la Configuracion Control de Estadísticos de Servicios creado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\RuntimeException $e) {
            if ($e->getCode() === 404) {
                $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            } elseif ($e->getCode() === 400) {
                $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            $response->getBody()->write(json_encode(['estado' => false, 'message' => 'Error interno del servidor.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function createControlEstadisticosServicios(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $entity = new ControlEstadisticosServicios();
        try {
            $controlServiciosDTO = new ControlEstadisticosServiciosDTO(
                null,
                "",
                $data['idDetalleZonaServicioHorario'],
                1,
                $data['cantidadFirmada'],
                $data['cantidadAnulada'],
                new \DateTime($data['fechaCorte']),
                new \DateTime(),
                null,
                $data['estado'] ?? true
            );

            $this->controlEstadisticosServiciosServices->createControlEstadisticosServicios($controlServiciosDTO);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Control de Estadísticos de Servicios creado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function updateControlEstadisticosServicios(Request $request, Response $response, array $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $id = (int) $args['id'];

        try {
            $entity = new ControlEstadisticosServicios();
            $controlServiciosDTO = new ControlEstadisticosServiciosDTO(
                $id,
                $entity->getUuid(),
                $data['idDetalleZonaServicioHorario'],
                $data['cantidadFirmada'],
                $data['cantidadAnulada'],
                $data['jsonConfiguracion'],
                $data['fechaCorte'],
                $data['fecha_creacion'] ?? new \DateTime(),
                $data['fecha_modificacion'] ?? new \DateTime(),
                $data['estado'] ?? true
            );

            $this->controlEstadisticosServiciosServices->updateControlEstadisticosServicios($id, $controlServiciosDTO);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Control de Estadísticos de Servicios actualizado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function deleteControlEstadisticosServicios(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        try {
            $this->controlEstadisticosServiciosServices->deleteControlEstadisticosServicios($id);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Control de Estadísticos de Servicios eliminado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
