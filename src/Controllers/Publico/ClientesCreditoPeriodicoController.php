<?php

namespace App\Controllers\Publico;

use App\DTO\Publico\ClientesCreditoPeriodicoDTO;
use App\Services\Publico\ClientesCreditoPeriodicoServices;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ClientesCreditoPeriodicoController
{
    private ClientesCreditoPeriodicoServices $clienteCreditoPeriodicoServices;

    // Constructor para inyectar el servicio ClienteCreditoPeriodicoServices
    public function __construct(ClientesCreditoPeriodicoServices $clienteCreditoPeriodicoServices)
    {
        $this->clienteCreditoPeriodicoServices = $clienteCreditoPeriodicoServices;
    }

    /**
     * Obtiene todos los registros de crédito periódico de clientes.
     */
    public function getAllClienteCreditoPeriodico(Request $request, Response $response): Response
    {
        try {
            $clientesCreditoPeriodico = $this->clienteCreditoPeriodicoServices->getAllClienteCreditoPeriodico();
            $response->getBody()->write(json_encode($clientesCreditoPeriodico));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Obtiene un solo registro de crédito periódico de cliente por ID.
     */
    public function getClienteCreditoPeriodicoById(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            $clienteCreditoPeriodico = $this->clienteCreditoPeriodicoServices->getClienteCreditoPeriodicoById($id);

            if ($clienteCreditoPeriodico === null) {
                $response->getBody()->write(json_encode(['estado' => false, 'message' => 'Crédito periódico no encontrado']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode($clienteCreditoPeriodico));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Crea nuevos registros de crédito periódico de clientes.
     */
    public function createClienteCreditoPeriodico(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);

        try {
            $this->validateClienteCreditoPeriodicoData($data);
            $clientesCreditoPeriodicoDTOs = [];
            foreach ($data as $creditoData) {
                $clienteCreditoPeriodicoDTO = new ClientesCreditoPeriodicoDTO(
                    null,
                    $creditoData['idDetalleZonaServicioHorarioClienteFacturacion'],
                    new \DateTime($creditoData['periodoInicial']),
                    new \DateTime($creditoData['periodoFinal']),
                    $creditoData['cantidadCreditoLimite'],
                    $creditoData['cantidadCreditoUsado'],
                    $creditoData['cantidadCreditoDisponible'],
                    new \DateTime($creditoData['fechaCreacion']),
                    isset($creditoData['fechaModificacion']) ? new \DateTime($creditoData['fechaModificacion']) : null,
                    $creditoData['estado'] ?? true
                );

                $clientesCreditoPeriodicoDTOs[] = $clienteCreditoPeriodicoDTO;
            }

            $this->clienteCreditoPeriodicoServices->createClienteCreditoPeriodico($clientesCreditoPeriodicoDTOs);

            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Créditos periódicos creados exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Actualiza un registro de crédito periódico de cliente por ID.
     */
    public function updateClienteCreditoPeriodico(Request $request, Response $response, array $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $id = (int) $args['id'];

        try {
            $this->validateClienteCreditoPeriodicoData($data);

            $clienteCreditoPeriodicoDTO = new ClientesCreditoPeriodicoDTO(
                $id,
                $data['idDetalleZonaServicioHorarioClienteFacturacion'],
                new \DateTime($data['periodoInicial']),
                new \DateTime($data['periodoFinal']),
                $data['cantidadCreditoLimite'],
                $data['cantidadCreditoUsado'],
                $data['cantidadCreditoDisponible'],
                new \DateTime($data['fechaCreacion']),
                isset($data['fechaModificacion']) ? new \DateTime($data['fechaModificacion']) : null,
                $data['estado'] ?? true
            );

            $this->clienteCreditoPeriodicoServices->updateClienteCreditoPeriodico($id, $clienteCreditoPeriodicoDTO);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Crédito periódico actualizado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Elimina un registro de crédito periódico de cliente por ID.
     */
    public function deleteClienteCreditoPeriodico(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        try {
            $this->clienteCreditoPeriodicoServices->deleteClienteCreditoPeriodico($id);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Crédito periódico eliminado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Validaciones de los datos de crédito periódico.
     */
    private function validateClienteCreditoPeriodicoData(array $data): void
    {
        if (empty($data)) {
            throw new \Exception("No se proporcionaron datos de crédito periódico.");
        }

        foreach ($data as $creditoData) {
            if (empty($creditoData['idDetalleZonaServicioHorarioClienteFacturacion']) || empty($creditoData['periodoInicial']) || empty($creditoData['periodoFinal'])) {
                throw new \Exception("Faltan campos obligatorios.");
            }
        }
    }
}
