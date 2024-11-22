<?php

namespace App\Controllers\Publico;

use App\DTO\Publico\ClientesDTO;
use App\Services\Publico\ClientesServices;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Repository\Publico\ClientesRepositoryInterface;
use App\Repository\Catalogo\ListaCatalogoDetalleRepositoryInterface;

class ClientesController
{
    private ClientesServices $clientesServices;

    public function __construct(
        ClientesServices $clientesServices
    ) {
        $this->clientesServices = $clientesServices;
    }

    public function getAllClientes(Request $request, Response $response): Response
    {
        try {
            $clientes = $this->clientesServices->getAllClientes();
            $response->getBody()->write(json_encode($clientes));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getClienteById(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            $cliente = $this->clientesServices->getClienteById($id);

            if ($cliente === null) {
                $response->getBody()->write(json_encode(['estado' => false, 'message' => 'Cliente no encontrado']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode($cliente));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function createCliente(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);

        try {
            $this->validateClientesData($data);
            $clientesDTOs = [];
            foreach ($data as $clienteData) {
                $clientesDTO = new ClientesDTO(
                    null,
                    $clienteData['nombres'],
                    $clienteData['apellidos'],
                    $clienteData['id_departamento'],
                    $clienteData['id_cargo'],
                    $clienteData['correo'],
                    $clienteData['clie_docnum'],
                    new \DateTime(),
                    null,
                    $clienteData['estado'] ?? true
                );

                $clientesDTOs[] = $clientesDTO;
            }

            $this->clientesServices->createCliente($clientesDTOs);

            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Clientes creados exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function updateCliente(Request $request, Response $response, array $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $id = (int) $args['id'];

        try {
            // Validaciones
            $this->validateClientesData($data);

            $clientesDTO = new ClientesDTO(
                $id,
                $data['nombres'],
                $data['apellidos'],
                $data['id_departamento'],
                $data['id_cargo'],
                $data['correo'],
                $data['clie_docnum'],
                $data['fecha_creacion'] ?? new \DateTime(),
                $data['fecha_modificacion'] ?? new \DateTime(),
                $data['estado'] ?? true
            );

            $this->clientesServices->updateCliente($id, $clientesDTO);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Cliente actualizado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function deleteCliente(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        try {
            $this->clientesServices->deleteCliente($id);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Cliente eliminado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    private function validateClientesData(array $data): void
    {
        if (empty($data)) {
            throw new \Exception("No se proporcionaron clientes.");
        }

        foreach ($data as $clienteData) {
            if (empty($clienteData['nombres']) || empty($clienteData['apellidos'])) {
                throw new \Exception("Faltan campos obligatorios.");
            }
        }
    }
}
