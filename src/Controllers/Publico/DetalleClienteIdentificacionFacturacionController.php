<?php

namespace App\Controllers\Publico;

use App\DTO\Publico\DetalleClienteIdentificacionFacturacionDTO;
use App\Services\Publico\DetalleClienteIdentificacionFacturacionServices;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DetalleClienteIdentificacionFacturacionController
{
    private DetalleClienteIdentificacionFacturacionServices $detalleClienteIdentificacionFacturacionServices;

    public function __construct(
        DetalleClienteIdentificacionFacturacionServices $detalleClienteIdentificacionFacturacionServices
    ) {
        $this->detalleClienteIdentificacionFacturacionServices = $detalleClienteIdentificacionFacturacionServices;
    }

    /**
     * Obtener todos los detalles de cliente identificación facturación
     */
    public function getAllDetalleClienteIdentificacionFacturacion(Request $request, Response $response): Response
    {
        try {
            $detalles = $this->detalleClienteIdentificacionFacturacionServices->getAllDetalleClienteIdentificacionFacturacion();
            $response->getBody()->write(json_encode($detalles));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Obtener un detalle de cliente identificación facturación por ID
     */
    public function getDetalleClienteIdentificacionFacturacionById(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            $detalle = $this->detalleClienteIdentificacionFacturacionServices->getDetalleClienteIdentificacionFacturacionById($id);

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
     * Crear un nuevo detalle de cliente identificación facturación
     */
    public function createDetalleClienteIdentificacionFacturacion(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);

        try {
            $this->validateDetalleClienteIdentificacionFacturacionData($data);
            $detallesDTOs = [];
            foreach ($data as $detalleData) {

                $jsonString = str_replace("'", '"', $detalleData['json_identificacion']);
                $json = json_decode($jsonString, true);

                $detalleDTO = new DetalleClienteIdentificacionFacturacionDTO(
                    null,  // ID se asignará automáticamente
                    $detalleData['id_cliente'],
                    $detalleData['id_identificacion_facturacion'],
                    $json,
                    new \DateTime(),  // Fecha de creación actual
                    null,  // Fecha de modificación no proporcionada
                    $detalleData['estado'] ?? true
                );

                $detallesDTOs[] = $detalleDTO;
                $this->detalleClienteIdentificacionFacturacionServices->createDetalleClienteIdentificacionFacturacion($detalleDTO);
            }
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Detalles creados exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Actualizar un detalle de cliente identificación facturación por ID
     */
    public function updateDetalleClienteIdentificacionFacturacion(Request $request, Response $response, array $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $id = (int) $args['id'];

        try {
            // Validaciones
            $this->validateDetalleClienteIdentificacionFacturacionData($data);

            $detalleDTO = new DetalleClienteIdentificacionFacturacionDTO(
                $id,  // Usamos el ID existente
                $data['id_cliente'],
                $data['id_identificacion_facturacion'],
                $data['json_identificacion'],
                $data['fecha_creacion'] ?? new \DateTime(),
                $data['fecha_modificacion'] ?? new \DateTime(),
                $data['estado'] ?? true
            );

            $this->detalleClienteIdentificacionFacturacionServices->updateDetalleClienteIdentificacionFacturacion($id, $detalleDTO);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Detalle actualizado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Eliminar un detalle de cliente identificación facturación por ID
     */
    public function deleteDetalleClienteIdentificacionFacturacion(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        try {
            $this->detalleClienteIdentificacionFacturacionServices->deleteDetalleClienteIdentificacionFacturacion($id);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Detalle eliminado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Validación de los datos de los detalles de cliente identificación facturación
     */
    private function validateDetalleClienteIdentificacionFacturacionData(array $data): void
    {
        if (empty($data)) {
            throw new \Exception("No se proporcionaron detalles.");
        }

        foreach ($data as $detalleData) {
            if (empty($detalleData['id_cliente']) || empty($detalleData['id_identificacion_facturacion']) || empty($detalleData['json_identificacion'])) {
                throw new \Exception("Faltan campos obligatorios.");
            }
        }
    }
}
