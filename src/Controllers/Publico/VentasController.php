<?php

namespace App\Controllers\Publico;

use App\DTO\Publico\VentasDTO;
use App\DTO\Publico\VentasFacturacionDTO;
use App\Services\AuthServices;
use App\Services\Publico\VentasServices;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteParser;

class VentasController
{
    private VentasServices $ventasServices;
    private ContainerInterface $container;
    private AuthServices $authServices;

    // Constructor para inyectar el servicio ventasServices
    public function __construct(ContainerInterface $container, VentasServices $ventasServices, AuthServices $authServices)
    {
        $this->container = $container;
        $this->ventasServices = $ventasServices;
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
        include __DIR__ . '/../../../public/views/client/inicio.php';
        $viewContent = ob_get_clean();
        $response->getBody()->write($viewContent);

        return $response->withHeader('Content-Type', 'text/html');
    }


    /**
     * Obtiene todos los registros de crédito periódico de ventas.
     */
    public function getAllVentas(Request $request, Response $response): Response
    {
        try {
            $clientesCreditoPeriodico = $this->ventasServices->getAllVentas();
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
    public function getVentaById(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            $clienteCreditoPeriodico = $this->ventasServices->getVentaById($id);

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
    public function createVenta(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);

        try {
            $this->validateClienteCreditoPeriodicoDataFacturacion($data);
            $ventasDTOs = [];
            foreach ($data as $item) {
                $ventaDTO = new VentasFacturacionDTO(
                    $item['cod_identificacion'],
                    $item['idDetalleZonaServicioHorario'],
                    $item['cantidadFacturada'],
                    $item['codigoInternoIF'],
                );

                $ventasDTOs[] = $ventaDTO;
            }

            $resultado = $this->ventasServices->createVenta($ventasDTOs);

            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Créditos periódicos creados exitosamente', 'data' => $resultado]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Actualiza un registro de crédito periódico de cliente por ID.
     */
    public function updateVenta(Request $request, Response $response, array $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $id = (int) $args['id'];

        try {
            $this->validateClienteCreditoPeriodicoData($data);

            $clienteCreditoPeriodicoDTO = new VentasDTO(
                $id,
                "",
                $data['idDetalleZonaServicioHorarioClienteFacturacion'],
                $data['idCliente'],
                $data['cantidadFacturada'],
                $data['ticketAnulado'],
                $data['cantidadAnulada'],
                new \DateTime($data['fechaEmision']),
                isset($data['fechaModificacion']) ? new \DateTime($data['fechaModificacion']) : null,
                $data['idEstadoVenta'],
                $data['estado'] ?? true
            );

            $this->ventasServices->updateVenta($id, $clienteCreditoPeriodicoDTO);
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
    public function deleteVenta(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        try {
            $this->ventasServices->deleteVenta($id);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Crédito periódico eliminado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getReportVentas(Request $request, Response $response, array $args): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $query = strtolower($queryParams['q'] ?? '');

            $filtro = (array) json_decode($query);
            $report = $this->ventasServices->getReportVentas($filtro);

            if ($report === null) {
                $response->getBody()->write(json_encode(['estado' => false, 'message' => 'Crédito periódico no encontrado']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode($report));
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

    private function validateClienteCreditoPeriodicoDataFacturacion(array $data): void
    {
        if (empty($data)) {
            throw new \Exception("No se proporcionaron datos de crédito periódico.");
        }

        foreach ($data as $creditoData) {
            if (empty($creditoData['idDetalleZonaServicioHorario']) || empty($creditoData['cantidadFacturada']) || empty($creditoData['cod_identificacion'])) {
                throw new \Exception("Faltan campos obligatorios.");
            }
        }
    }
}
