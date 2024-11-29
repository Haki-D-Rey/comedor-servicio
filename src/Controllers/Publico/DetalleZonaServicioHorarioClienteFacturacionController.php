<?php

namespace App\Controllers\Publico;

use App\DTO\Publico\DetalleZonaServicioHorarioClienteFacturacionDTO;
use App\Services\Publico\DetalleZonaServicioHorarioClienteFacturacionServices;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DetalleZonaServicioHorarioClienteFacturacionController
{
    private DetalleZonaServicioHorarioClienteFacturacionServices $detalleZonaServicioHorarioClienteFacturacionServices;

    public function __construct(
        DetalleZonaServicioHorarioClienteFacturacionServices $detalleZonaServicioHorarioClienteFacturacionServices
    ) {
        $this->detalleZonaServicioHorarioClienteFacturacionServices = $detalleZonaServicioHorarioClienteFacturacionServices;
    }

    /**
     * Obtener todos los detalles de zona servicio horario cliente facturación
     */
    public function getAllDetalleZonaServicioHorarioClienteFacturacion(Request $request, Response $response): Response
    {
        try {
            $detalles = $this->detalleZonaServicioHorarioClienteFacturacionServices->getAllDetalleZonaServicioHorarioClienteFacturacion();
            $response->getBody()->write(json_encode($detalles));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Obtener un detalle de zona servicio horario cliente facturación por ID
     */
    public function getDetalleZonaServicioHorarioClienteFacturacionById(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            $detalle = $this->detalleZonaServicioHorarioClienteFacturacionServices->getDetalleZonaServicioHorarioClienteFacturacionById($id);

            if ($detalle === null) {
                $response->getBody()->write(json_encode(['estado' => false, 'message' => 'Detalle no encontrado']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode($detalle));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Crear un nuevo detalle de zona servicio horario cliente facturación
     */
    public function createDetalleZonaServicioHorarioClienteFacturacion(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);

        try {
            $this->validateDetalleZonaServicioHorarioClienteFacturacionData($data);
            $detallesDTOs = [];
            foreach ($data as $detalleData) {

                $detalleDTO = new DetalleZonaServicioHorarioClienteFacturacionDTO(
                    null,  // ID se asignará automáticamente
                    $detalleData['id_detalle_cliente_identificacion_facturacion'],
                    $detalleData['id_detalle_zona_servicio_horario'],
                    $detalleData['codigo_interno'],
                    new \DateTime(),  // Fecha de creación actual
                    null,  // Fecha de modificación no proporcionada
                    $detalleData['estado'] ?? true
                );

                $detallesDTOs[] = $detalleDTO;
                $this->detalleZonaServicioHorarioClienteFacturacionServices->createDetalleZonaServicioHorarioClienteFacturacion($detalleDTO);
            }
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Detalles creados exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Actualizar un detalle de zona servicio horario cliente facturación por ID
     */
    public function updateDetalleZonaServicioHorarioClienteFacturacion(Request $request, Response $response, array $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $id = (int) $args['id'];

        try {
            // Validaciones
            $this->validateDetalleZonaServicioHorarioClienteFacturacionData($data);

            $detalleDTO = new DetalleZonaServicioHorarioClienteFacturacionDTO(
                $id,  // Usamos el ID existente
                $data['id_detalle_cliente_identificacion_facturacion'],
                $data['id_detalle_zona_servicio_horario'],
                $data['codigo_interno'],
                $data['fecha_creacion'] ?? new \DateTime(),
                $data['fecha_modificacion'] ?? new \DateTime(),
                $data['estado'] ?? true
            );

            $this->detalleZonaServicioHorarioClienteFacturacionServices->updateDetalleZonaServicioHorarioClienteFacturacion($id, $detalleDTO);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Detalle actualizado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Eliminar un detalle de zona servicio horario cliente facturación por ID
     */
    public function deleteDetalleZonaServicioHorarioClienteFacturacion(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        try {
            $this->detalleZonaServicioHorarioClienteFacturacionServices->deleteDetalleZonaServicioHorarioClienteFacturacion($id);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Detalle eliminado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Validación de los datos de los detalles de zona servicio horario cliente facturación
     */
    private function validateDetalleZonaServicioHorarioClienteFacturacionData(array $data): void
    {
        if (empty($data)) {
            throw new \Exception("No se proporcionaron detalles.");
        }

        foreach ($data as $detalleData) {
            if (empty($detalleData['id_detalle_cliente_identificacion_facturacion']) || empty($detalleData['id_detalle_zona_servicio_horario']) || empty($detalleData['codigo_interno'])) {
                throw new \Exception("Faltan campos obligatorios.");
            }
        }
    }
}
