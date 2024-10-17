<?php

namespace App\Controllers;

use App\DTO\Publico\ConfiguracionServiciosEstadisticosDTO;
use App\Entity\Publico\ConfiguracionServiciosEstadisticos;
use App\Entity\Publico\ControlEstadisticosServicios;
use App\Services\ConfiguracionServiciosEstadisticosServices;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ConfiguracionServiciosEstadisticosController
{
    private ConfiguracionServiciosEstadisticosServices $configuracionServiciosEstadisticosServices;

    public function __construct(ConfiguracionServiciosEstadisticosServices $configuracionServiciosEstadisticosServices)
    {
        $this->configuracionServiciosEstadisticosServices = $configuracionServiciosEstadisticosServices;
    }

    public function getAllConfiguracionServiciosEstadisticos(Request $request, Response $response): Response
    {
        try {
            $servicios = $this->configuracionServiciosEstadisticosServices->getAllConfiguracionServiciosEstadisticos();
            $response->getBody()->write(json_encode($servicios));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getConfiguracionServiciosEstadisticosById(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            $servicio = $this->configuracionServiciosEstadisticosServices->getControlEstadisticosServiciosById($id);

            if ($servicio === null) {
                $response->getBody()->write(json_encode(['estado' => false, 'message' => 'La Configuracion Control de Estadísticos de Servicios no encontrado']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode($servicio));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function createConfiguracionServiciosEstadisticos(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        try {
            $configuracionServiciosEstadisticosDTO = new ConfiguracionServiciosEstadisticosDTO(
                null,
                $data['jsonConfiguracion'],
                $data['fechaCorte'],
                new \DateTime(),
                null,
                $data['estado'] ?? true
            );

            $resultado = $this->configuracionServiciosEstadisticosServices->createConfiguracionServiciosEstadisticos($configuracionServiciosEstadisticosDTO);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'La Configuracion Control de Estadísticos de Servicios creado exitosamente', 'data' => $resultado]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function updateConfiguracionServiciosEstadisticos(Request $request, Response $response, array $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $id = (int) $args['id'];

        try {
            $entity = new ConfiguracionServiciosEstadisticos();
            $controlServiciosDTO = new ConfiguracionServiciosEstadisticosDTO(
                $id,
                $data['jsonConfiguracion'],
                $data['fechaCorte'],
                $data['fecha_creacion'] ?? new \DateTime(),
                $data['fecha_modificacion'] ?? new \DateTime(),
                $data['estado'] ?? true
            );

            $this->configuracionServiciosEstadisticosServices->updateConfiguracionServiciosEstadisticos($id, $controlServiciosDTO);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'La Configuracion Control de Estadísticos de Servicios actualizado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function deleteConfiguracionServiciosEstadisticos(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        try {
            $this->configuracionServiciosEstadisticosServices->deleteConfiguracionServiciosEstadisticos($id);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'La Configuracion Control de Estadísticos de Servicios eliminado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
