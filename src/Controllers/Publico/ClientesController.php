<?php

namespace App\Controllers\Publico;

use App\DTO\Publico\ClientesDTO;
use App\Services\AuthServices;
use App\Services\Publico\ClientesServices;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;
use Slim\Psr7\Request;
use Slim\Routing\RouteParser;

class ClientesController
{
    private ClientesServices $clientesServices;
    private ContainerInterface $container;
    private AuthServices $authServices;

    public function __construct(
        ClientesServices $clientesServices,
        ContainerInterface $container,
        AuthServices $authServices
    ) {
        $this->clientesServices = $clientesServices;
        $this->container = $container;
        $this->authServices = $authServices;
    }

    public function index(Request $request, Response $response): Response
    {
        session_start();
        $routeParser = $this->container->get(RouteParser::class);
        $data = [
            "token" => $_SESSION['jwt_token']
        ];
        $user_id = $this->authServices->verifyToken($data, $this->container)['user']->sub;
        ob_start();
        // include __DIR__ . '/views/client/inscripcion_control.php';
        include __DIR__ . '/../../../public/views/admin/clients/inicio.php';
        $viewContent = ob_get_clean();
        $response->getBody()->write($viewContent);

        return $response->withHeader('Content-Type', 'text/html');
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
            foreach ($data as $cliente) {
                $clientesDTO = new ClientesDTO(
                    $id,
                    $cliente['nombres'],
                    $cliente['apellidos'],
                    $cliente['id_departamento'],
                    $cliente['id_cargo'],
                    $cliente['correo'],
                    $cliente['clie_docnum'],
                    $cliente['fecha_creacion'] ?? new \DateTime(),
                    $cliente['fecha_modificacion'] ?? new \DateTime(),
                    $cliente['estado'] ?? true
                );
            }


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

    public function getSearchClients(Request $request, Response $response): Response
    {
        try {

            $queryParams = $request->getQueryParams();
            $query = strtolower($queryParams['q'] ?? '');

            if (empty($query)) {
                $response->getBody()->write(json_encode(['estado' => false, 'message' => 'Parámetro de búsqueda vacío']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $partes = explode('-', $query);
            $nombreBusqueda = $partes[0] ?? '';
            $apellidoBusqueda = $partes[1] ?? '';

            $filtro = ["nombres" => $nombreBusqueda, "apellidos" => $apellidoBusqueda];
            $clientes = $this->clientesServices->getSearchClients($filtro);

            $resultados = array_filter($clientes, function ($cliente) use ($nombreBusqueda, $apellidoBusqueda) {
                $nombreCliente = strtolower(trim($cliente->getNombres() ?? ''));
                $apellidoCliente = strtolower(trim($cliente->getApellidos() ?? ''));

                $nombreBusqueda = trim($nombreBusqueda ?? '');
                $apellidoBusqueda = trim($apellidoBusqueda ?? '');

                $similitudNombre = 0;
                $similitudApellido = 0;

                if ($nombreCliente == 'cesar adan') {
                    $l = 1;
                }

                if (!empty($nombreBusqueda) && !empty($apellidoBusqueda)) {
                    similar_text($nombreBusqueda, $nombreCliente, $similitudNombre);
                    similar_text($apellidoBusqueda, $apellidoCliente, $similitudApellido);
                    return $similitudNombre > 48 && $similitudApellido > 48;
                }

                if (!empty($nombreBusqueda) && empty($apellidoBusqueda)) {
                    similar_text($nombreBusqueda, $nombreCliente, $similitudNombre);
                    return $similitudNombre > 90 || strpos($nombreCliente, $nombreBusqueda) !== false;
                }

                if (empty($nombreBusqueda) && !empty($apellidoBusqueda)) {
                    similar_text($apellidoBusqueda, $apellidoCliente, $similitudApellido);
                    return $similitudApellido > 90 || strpos($apellidoCliente, $apellidoBusqueda) !== false;
                }

                return false;
            });


            $resultados = array_map(function ($cliente) {
                return [
                    'id' => $cliente->getId(),
                    'nombres' => $cliente->getNombres(),
                    'apellidos' => $cliente->getApellidos()
                ];
            }, $resultados);

            $response->getBody()->write(json_encode(array_values($resultados)));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }


    public function getClientsRelationalIdentification(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $query = strtolower($queryParams['q'] ?? '');

            $filtro = json_decode($query);
            $clientes = $this->clientesServices->getClientsRelationalIdentification($filtro);
            $response->getBody()->write(json_encode($clientes));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getValidateFormById(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $query = strtolower($queryParams['q'] ?? '');

            $filtro = json_decode($query);
            $clientes = $this->clientesServices->getValidateFormById($filtro);
            $response->getBody()->write(json_encode($clientes));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
