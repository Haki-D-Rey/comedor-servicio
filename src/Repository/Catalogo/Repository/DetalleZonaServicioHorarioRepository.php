<?php

namespace App\Repository\Catalogo\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use App\DTO\DetalleZonasServicioHorarioDTO;
use App\DTO\ServiciosProductosDetallesDTO;
use App\Entity\DetalleZonaServicioHorario;
use App\Entity\ServiciosProductosDetalles;
use App\Entity\ZonaUsuarios;
use App\Repository\Catalogo\Interface\DetalleZonaServicioHorarioRepositoryInterface;
use App\Repository\GenericRepository;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface as LogLoggerInterface;

class DetalleZonaServicioHorarioRepository extends GenericRepository implements DetalleZonaServicioHorarioRepositoryInterface
{
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LogLoggerInterface $loggerInterface)
    {
        parent::__construct($entityManager, DetalleZonaServicioHorario::class);
        $this->logger = $loggerInterface;
    }

    public function getAllDetalleZonasServicioHorario(): array
    {
        try {
            $detalleZonasServicioHorario = $this->getAllEntities();
            return array_map(function (DetalleZonaServicioHorario $detalleZonasServicioHorario) {
                return new DetalleZonasServicioHorarioDTO(
                    $detalleZonasServicioHorario->getId(),
                    $detalleZonasServicioHorario->getIdServiciosProductosDetalles(),
                    $detalleZonasServicioHorario->getIdHorario(),
                    $detalleZonasServicioHorario->getIdZonaUsuario(),
                    $detalleZonasServicioHorario->getNombre(),
                    $detalleZonasServicioHorario->getDescripcion(),
                    $detalleZonasServicioHorario->getCodigoInterno(),
                    $detalleZonasServicioHorario->getFechaCreacion(),
                    $detalleZonasServicioHorario->getFechaModificacion(),
                    $detalleZonasServicioHorario->getEstado()
                );
            }, $detalleZonasServicioHorario);
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener el Catalago del Detalle de los Servicios de Productos: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function getAllDetalleZonaServicioHorarioByZonaUsuario($idUsuario): array
    {
        // Buscar todas las zonas asociadas al usuario
        $zonasUsuario = $this->entityManager->getRepository(ZonaUsuarios::class)
            ->findBy(['usuario' => $idUsuario, 'estado' => true]); // Asegúrate de que 'usuario' sea el campo correcto en la entidad ZonaUsuarios.

        if (empty($zonasUsuario)) {
            return [];
        }

        // Extraer los IDs de las zonas
        $zonaIds = array_map(function (ZonaUsuarios $zona) {
            return $zona->getId();
        }, $zonasUsuario);

        // Buscar todos los detalles de zona y servicio horario asociados a las zonas encontradas
        $detalleZonaServicioHorarios = $this->findBy(['zonaUsuario' => $zonaIds]);

        if (empty($detalleZonaServicioHorarios)) {
            return [];
        }

        // Transformar a DTO
        $detalleZonaServicioHorarioDTO = array_map(function (DetalleZonaServicioHorario $detalleZonaServicioHorario) {
            return [
                'id_detalle_zona_servicio_horario' => $detalleZonaServicioHorario->getId(),
                'id_servicio_detalle_producto' => $detalleZonaServicioHorario->getIdServiciosProductosDetalles(),
                'idZonaUsuario' => $detalleZonaServicioHorario->getIdZonaUsuario(),
            ];
        }, $detalleZonaServicioHorarios);

        // Obtener todos los ID de servicio detalle producto
        $idServiciosDetalles = array_map(function ($detalleZonaServicioHorarioDTO) {
            return $detalleZonaServicioHorarioDTO['id_servicio_detalle_producto'];
        }, $detalleZonaServicioHorarioDTO);

        // Buscar todos los servicios producto detalles correspondientes
        $servicioProductoDetalles = $this->entityManager->getRepository(ServiciosProductosDetalles::class)
            ->findBy(['id' => $idServiciosDetalles]);

        if (empty($servicioProductoDetalles)) {
            return [];
        }

        // Preparar el resultado final
        $resultadoFinal = [];

        foreach ($detalleZonaServicioHorarioDTO as $detalle) {
            foreach ($servicioProductoDetalles as $servicioProductoDetalle) {
                if ($detalle['id_servicio_detalle_producto'] == $servicioProductoDetalle->getId()) {
                    $resultadoFinal[] = [
                        'id_detalle_zona_servicio_horario' => $detalle['id_detalle_zona_servicio_horario'],
                        'id_servicio_detalle_producto' => $detalle['id_servicio_detalle_producto'],
                        'idZonaUsuario' => $detalle['idZonaUsuario'],
                        'idSistemas' => $servicioProductoDetalle->getIdSistemas(),
                        'idTipoServicios' => $servicioProductoDetalle->getIdTipoServicios(),
                        'idServiciosProductos' => $servicioProductoDetalle->getIdServiciosProductos(),
                        'nombre' => $servicioProductoDetalle->getNombre(),
                        'descripcion' => $servicioProductoDetalle->getDescripcion(),
                        'codigo_interno' => $servicioProductoDetalle->getCodigo_interno(),
                        'fecha_creacion' => $servicioProductoDetalle->getFecha_creacion(),
                        'fecha_modificacion' => $servicioProductoDetalle->getFecha_modificacion(),
                        'estado' => $servicioProductoDetalle->getEstado(),
                    ];
                }
            }
        }

        return $resultadoFinal;
    }

    public function getAllDetalleZonaServicioHorarioByIdZonaUsuario($id): array
    {
        $zonasUsuario = $this->entityManager->getRepository(ZonaUsuarios::class)->findBy(['codigo_interno' => $id, 'estado' => true]);

        if (empty($zonasUsuario)) {
            return [];
        }

        $detalleZonaServicioHorarios = $this->findBy(['zonaUsuario' => $zonasUsuario[0]->getId()]);

        if (empty($detalleZonaServicioHorarios)) {
            return [];
        }

        $detalleZonaServicioHorarioDTO = array_map(function (DetalleZonaServicioHorario $detalleZonaServicioHorario) {
            return [
                'id_detalle_zona_servicio_horario' => $detalleZonaServicioHorario->getId(),
                'id_servicio_detalle_producto' => $detalleZonaServicioHorario->getIdServiciosProductosDetalles(),
                'idZonaUsuario' => $detalleZonaServicioHorario->getIdZonaUsuario(),
            ];
        }, $detalleZonaServicioHorarios);

        $idServiciosDetalles = array_map(function ($detalleZonaServicioHorarioDTO) {
            return $detalleZonaServicioHorarioDTO['id_servicio_detalle_producto'];
        }, $detalleZonaServicioHorarioDTO);

        $servicioProductoDetalles = $this->entityManager->getRepository(ServiciosProductosDetalles::class)->findBy(['id' => $idServiciosDetalles]);

        if (empty($servicioProductoDetalles)) {
            return [];
        }

        $resultadoFinal = [];

        foreach ($detalleZonaServicioHorarioDTO as $detalle) {
            foreach ($servicioProductoDetalles as $servicioProductoDetalle) {
                if ($detalle['id_servicio_detalle_producto'] == $servicioProductoDetalle->getId()) {
                    $resultadoFinal[] = [
                        'id_detalle_zona_servicio_horario' => $detalle['id_detalle_zona_servicio_horario'],
                        'id_servicio_detalle_producto' => $detalle['id_servicio_detalle_producto'],
                        'idZonaUsuario' => $detalle['idZonaUsuario'],
                        'idSistemas' => $servicioProductoDetalle->getIdSistemas(),
                        'idTipoServicios' => $servicioProductoDetalle->getIdTipoServicios(),
                        'idServiciosProductos' => $servicioProductoDetalle->getIdServiciosProductos(),
                        'nombre' => $servicioProductoDetalle->getNombre(),
                        'descripcion' => $servicioProductoDetalle->getDescripcion(),
                        'codigo_interno' => $servicioProductoDetalle->getcodigo_interno(),
                        'fecha_creacion' => $servicioProductoDetalle->getFecha_creacion(),
                        'fecha_modificacion' => $servicioProductoDetalle->getFecha_modificacion(),
                        'estado' => $servicioProductoDetalle->getEstado()
                    ];
                }
            }
        }

        return $resultadoFinal;
    }

    public function getDetalleZonasServicioHorarioById(int $id): ?DetalleZonasServicioHorarioDTO
    {
        try {
            $detalleZonasServicioHorario = $this->getEntityById($id);
            if (!$detalleZonasServicioHorario) {
                return null;
            }

            return new DetalleZonasServicioHorarioDTO(
                $detalleZonasServicioHorario->getId(),
                $detalleZonasServicioHorario->getIdServiciosProductosDetalles(),
                $detalleZonasServicioHorario->getIdHorario(),
                $detalleZonasServicioHorario->getIdZonaUsuario(),
                $detalleZonasServicioHorario->getNombre(),
                $detalleZonasServicioHorario->getDescripcion(),
                $detalleZonasServicioHorario->getcodigo_interno(),
                $detalleZonasServicioHorario->getFecha_creacion(),
                $detalleZonasServicioHorario->getFecha_modificacion(),
                $detalleZonasServicioHorario->getEstado()
            );
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener el Servicio del Producto por ID: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function createDetalleZonasServicioHorario(DetalleZonasServicioHorarioDTO $detalleZonasServicioHorarioDTO): void
    {
        try {

            $existingcodigo_interno = $this->entityManager->getRepository(DetalleZonaServicioHorario::class)
                ->findOneBy(['codigo_interno' => $detalleZonasServicioHorarioDTO->getcodigo_interno()]);

            if ($existingcodigo_interno) {
                throw new \RuntimeException('El Codigo Interno ya está en uso.');
            }

            $existingNombre = $this->entityManager->getRepository(DetalleZonaServicioHorario::class)
                ->findOneBy(['nombre' => $detalleZonasServicioHorarioDTO->getNombre()]);

            if ($existingNombre) {
                throw new \RuntimeException('El Nombre ya está en uso.');
            }

            $detalleZonasServicioHorario = new DetalleZonaServicioHorario();
            $detalleZonasServicioHorario->setIdServiciosProductosDetalles($detalleZonasServicioHorarioDTO->getIdServiciosProductoDetalles());
            $detalleZonasServicioHorario->setIdHorario($detalleZonasServicioHorarioDTO->getIdHorario());
            $detalleZonasServicioHorario->setIdZonaUsuario($detalleZonasServicioHorarioDTO->getIdZonaUsuario());
            $detalleZonasServicioHorario->setNombre($detalleZonasServicioHorarioDTO->getNombre());
            $detalleZonasServicioHorario->setDescripcion($detalleZonasServicioHorarioDTO->getDescripcion());
            $detalleZonasServicioHorario->setCodigoInterno($detalleZonasServicioHorarioDTO->getcodigo_interno());
            $detalleZonasServicioHorario->setFechaCreacion($detalleZonasServicioHorarioDTO->getFecha_creacion());
            $detalleZonasServicioHorario->setFechaModificacion($detalleZonasServicioHorarioDTO->getFecha_modificacion());
            $detalleZonasServicioHorario->setEstado($detalleZonasServicioHorarioDTO->getEstado());

            $this->entityManager->persist($detalleZonasServicioHorario);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al crear el Servicio del Producto: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al crear usuario.');
        } catch (\Throwable $e) {
            $this->logger->error('Error inesperado al crear el Servicio del Producto: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function updateDetalleZonasServicioHorario(int $id, DetalleZonasServicioHorarioDTO $detalleZonasServicioHorarioDTO): void
    {
        try {
            $detalleZonasServicioHorario = $this->getEntityById($id);
            if (!$detalleZonasServicioHorario) {
                throw new \RuntimeException('El Servicio del Producto no encontrado.');
            }

            $existingcodigo_interno = $this->entityManager->getRepository(DetalleZonaServicioHorario::class)
                ->findOneBy(['codigo_interno' => $detalleZonasServicioHorarioDTO->getcodigo_interno()]);

            if ($existingcodigo_interno) {
                throw new \RuntimeException('El Codigo Interno ya está en uso.');
            }


            $existingNombre = $this->entityManager->getRepository(DetalleZonaServicioHorario::class)
                ->findOneBy(['nombre' => $detalleZonasServicioHorarioDTO->getNombre()]);


            if ($existingNombre) {
                throw new \RuntimeException('El Nombre ya está en uso.');
            }

            $excludePropertyName = ['fecha_creacion'];
            $this->updateEntityFromDTO($detalleZonasServicioHorario, $detalleZonasServicioHorarioDTO, $excludePropertyName);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al actualizar el Servicio del Producto: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function deleteDetalleZonasServicioHorario(int $id): void
    {
        try {
            $this->markAsDeleted($id, 0);
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al eliminar el Servicio del Producto: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }
}
