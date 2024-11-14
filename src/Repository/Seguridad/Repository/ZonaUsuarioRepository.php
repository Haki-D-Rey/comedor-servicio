<?php

namespace App\Repository\Seguridad\Repository;

use App\DTO\UsuarioDTO;
use App\DTO\ZonaDTO;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use App\DTO\ZonaUsuarioDTO;
use App\Entity\Usuario;
use App\Entity\Zona;
use App\Entity\ZonaUsuarios;
use App\Repository\GenericRepository;
use App\Repository\Seguridad\Interface\ZonaUsuarioRepositoryInterface;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface as LogLoggerInterface;

class ZonaUsuarioRepository extends GenericRepository implements ZonaUsuarioRepositoryInterface
{
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LogLoggerInterface $loggerInterface)
    {
        parent::__construct($entityManager, ZonaUsuarios::class);
        $this->logger = $loggerInterface;
    }

    public function getAllZonaUsuarios(): array
    {
        try {
            $zonaUsuarios = $this->getAllEntities();
            return array_map(function (ZonaUsuarios $zonaUsuario) {
                return new ZonaUsuarioDTO(
                    $zonaUsuario->getId(),
                    $zonaUsuario->getIdUsuario(),
                    $zonaUsuario->getIdZona(),
                    $zonaUsuario->getCodigoInterno(),
                    $zonaUsuario->getFechaCreacion(),
                    $zonaUsuario->getFechaModificacion(),
                    $zonaUsuario->getEstado()
                );
            }, $zonaUsuarios);
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener el Catalago de la relacion Zona Usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function getRelationalZonaUsuarioById(int $id): array
    {
        $usuario = $this->entityManager->getRepository(Usuario::class)->find($id);
        $usuarioDTO = new UsuarioDTO(
            $usuario->getId(),
            $usuario->getNombreUsuario(),
            $usuario->getContrasenia(),
            $usuario->getNombres(),
            $usuario->getApellidos(),
            $usuario->getCorreo(),
            $usuario->getFechaCreacion(),
            $usuario->getFechaModificacion(),
            $usuario->getIsAdmin(),
            $usuario->getEstado(),
            $usuario->getIdTipoUsuario()
        );

        if (!$usuarioDTO) {
            return [
                'usuario' => null,
                'idZonaUsuario' => null,
                'zonas' => [],
            ];
        }
        $zonaUsuarios = $this->findBy(['id_usuario' => $id, 'estado' => true]);

        if (!$zonaUsuarios) {
            return [
                'usuario' => $usuarioDTO,
                'idZonaUsuario' => null,
                'zonas' => [],
            ];
        }
        $idsZona = array_map(function ($zonaUsuario) {
            return $zonaUsuario->getIdZona();
        }, $zonaUsuarios);

        if (!empty($idsZona)) {
            $zonas = $this->entityManager->getRepository(Zona::class)->findBy([
                'id' => $idsZona,
                'estado' => true,
            ]);
        } else {
            $zonas = [];
        }

        $zonasDTO = array_map(function ($zonaUsuario) use ($zonas) {
            // Buscar la zona correspondiente al idZona del objeto $zonaUsuario
            $zonaCorrespondiente = array_filter($zonas, function ($zona) use ($zonaUsuario) {
                return $zona->getId() === $zonaUsuario->getIdZona();
            });

            // Si se encuentra la zona correspondiente, la transformamos en DTO
            if ($zonaCorrespondiente) {
                $zonaCorrespondiente = array_shift($zonaCorrespondiente); // Obtener el primer elemento
                return [
                    "idZonaUsuario" => $zonaUsuario->getId(),
                    "codigoInternoZonaUsuario" => $zonaUsuario->getCodigoInterno(),
                    "zonas" => new ZonaDTO(
                        $zonaCorrespondiente->getId(),
                        $zonaCorrespondiente->getNombre(),
                        $zonaCorrespondiente->getDescripcion(),
                        $zonaCorrespondiente->getcodigo_interno(),
                        $zonaCorrespondiente->getFecha_creacion(),
                        $zonaCorrespondiente->getFecha_modificacion(),
                        $zonaCorrespondiente->getEstado()
                    )
                ];
            }

            // Si no se encuentra una zona, retornar un valor nulo
            return [
                "idZonaUsuario" => $zonaUsuario->getId(),
                "codigoInternoZonaUsuario" => $zonaUsuario->getCodigoInterno(),
                "zonas" => null
            ];
        }, $zonaUsuarios);

        return [
            'DatosUsuario' => $usuarioDTO,  
            'DetallesZonas' => $zonasDTO,
        ];
    }


    public function getZonaUsuarioById(int $id): ?ZonaUsuarioDTO
    {
        try {
            $zonaUsuario = $this->getEntityById($id);
            if (!$zonaUsuario) {
                return null;
            }

            return new ZonaUsuarioDTO(
                $zonaUsuario->getId(),
                $zonaUsuario->getIdUsuario(),
                $zonaUsuario->getIdZona(),
                $zonaUsuario->getCodigoInterno(),
                $zonaUsuario->getFechaCreacion(),
                $zonaUsuario->getFechaModificacion(),
                $zonaUsuario->getEstado()
            );
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener la relacion Zona Usuario por ID: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function createZonaUsuario(ZonaUsuarioDTO $zonaUsuarioDTO): void
    {
        try {

            $existingcodigo_interno = $this->entityManager->getRepository(ZonaUsuarios::class)
                ->findOneBy(['codigo_interno' => $zonaUsuarioDTO->getCodigoInterno()]);

            if ($existingcodigo_interno) {
                throw new \RuntimeException('El Codigo Interno ya está en uso.');
            }

            $zonaUsuarios = new ZonaUsuarios();
            $zonaUsuarios->setIdUsuario($zonaUsuarioDTO->getIdUsuario());
            $zonaUsuarios->setIdZona($zonaUsuarioDTO->getIdZona());
            $zonaUsuarios->setCodigoInterno($zonaUsuarioDTO->getCodigoInterno());
            $zonaUsuarios->setFechaCreacion($zonaUsuarioDTO->getFechaCreacion());
            $zonaUsuarios->setFechaModificacion($zonaUsuarioDTO->getFechaModificacion());
            $zonaUsuarios->setEstado($zonaUsuarioDTO->getEstado());
            $this->entityManager->persist($zonaUsuarios);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al crear la relacion Zona Usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al crear la relacion Zona Usuario.');
        } catch (\Throwable $e) {
            $this->logger->error('Error inesperado al crear la relacion Zona Usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function updateZonaUsuario(int $id, ZonaUsuarioDTO $zonaUsuarioDTO): void
    {
        try {
            $zonaUsuarios = $this->getEntityById($id);
            if (!$zonaUsuarios) {
                throw new \RuntimeException('El Servicio del Producto no encontrado.');
            }

            $existingcodigo_interno = $this->entityManager->getRepository(ZonaUsuarios::class)
                ->findOneBy(['codigo_interno' => $zonaUsuarioDTO->getCodigoInterno()]);

            if ($existingcodigo_interno) {
                throw new \RuntimeException('El Codigo Interno ya está en uso.');
            }

            $excludePropertyName = ['fecha_creacion'];
            $this->updateEntityFromDTO($zonaUsuarios, $zonaUsuarioDTO, $excludePropertyName);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al actualizar la relacion Zona Usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function deleteZonaUsuario(int $id): void
    {
        try {
            $this->markAsDeleted($id, 0);
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al eliminar el Servicio del Producto: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }
}
