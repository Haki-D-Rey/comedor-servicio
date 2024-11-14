<?php

namespace App\Repository\Catalogo\Repository;

use App\DTO\Catalogo\TipoUsuarioDTO;
use App\Entity\Seguridad\TipoUsuario;
use App\Repository\Catalogo\Interface\TipoUsuariosRepositoryInterface;
use App\Repository\GenericRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface as LogLoggerInterface;

class TipoUsuariosRepository extends GenericRepository implements TipoUsuariosRepositoryInterface
{
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LogLoggerInterface $loggerInterface)
    {
        parent::__construct($entityManager, TipoUsuario::class);
        $this->logger = $loggerInterface;
    }

    public function getAllTipoUsuarios(): array
    {
        try {
            $tipoUsuario = $this->getAllEntities();
            return array_map(function (TipoUsuario $tipoUsuario) {
                return new TipoUsuarioDTO(
                    $tipoUsuario->getId(),
                    $tipoUsuario->getNombre(),
                    $tipoUsuario->getDescripcion(),
                    $tipoUsuario->getCodigoInterno(),
                    $tipoUsuario->getFechaCreacion(),
                    $tipoUsuario->getFechaModificacion(),
                    $tipoUsuario->getEstado()
                );
            }, $tipoUsuario);
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener usuarios: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function getTipoUsuarioById(int $id): ?TipoUsuarioDTO
    {
        try {
            $tipoUsuario = $this->getEntityById($id);
            if (!$tipoUsuario) {
                return null;
            }

            return new TipoUsuarioDTO(
                $tipoUsuario->getId(),
                $tipoUsuario->getNombre(),
                $tipoUsuario->getDescripcion(),
                $tipoUsuario->getCodigoInterno(),
                $tipoUsuario->getFechaCreacion(),
                $tipoUsuario->getFechaModificacion(),
                $tipoUsuario->getEstado()
            );
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener usuario por ID: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function createTipoUsuario(TipoUsuarioDTO $tipoUsuarioDTO): void
    {
        try {

            $existingcodigo_interno = $this->entityManager->getRepository(TipoUsuario::class)
                ->findOneBy(['codigo_interno' => $tipoUsuarioDTO->getCodigoInterno()]);

            if ($existingcodigo_interno) {
                throw new \RuntimeException('El Codigo Interno ya está en uso.');
            }


            $existingNombre = $this->entityManager->getRepository(TipoUsuario::class)
                ->findOneBy(['nombre' => $tipoUsuarioDTO->getNombre()]);


            if ($existingNombre) {
                throw new \RuntimeException('El Nombre ya está en uso.');
            }

            $tipoUsuario = new TipoUsuario();
            $tipoUsuario->setNombre($tipoUsuarioDTO->getNombre());
            $tipoUsuario->setDescripcion($tipoUsuarioDTO->getDescripcion());
            $tipoUsuario->setCodigoInterno($tipoUsuarioDTO->getCodigoInterno());
            $tipoUsuario->setFechaCreacion($tipoUsuarioDTO->getFechaCreacion());
            $tipoUsuario->setFechaModificacion($tipoUsuarioDTO->getFechaModificacion());
            $tipoUsuario->setEstado($tipoUsuarioDTO->getEstado());

            $this->entityManager->persist($tipoUsuario);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al crear usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al crear usuario.');
        } catch (\Throwable $e) {
            $this->logger->error('Error inesperado al crear usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function updateTipoUsuario(int $id, TipoUsuarioDTO $tipoUsuarioDTO): void
    {
        try {
            $tipoUsuario = $this->getEntityById($id);
            if (!$tipoUsuario) {
                throw new \RuntimeException('Usuario no encontrado.');
            }

            $existingcodigo_interno = $this->entityManager->getRepository(TipoUsuario::class)
                ->findOneBy(['codigo_interno' => $tipoUsuarioDTO->getCodigoInterno()]);

            if ($existingcodigo_interno) {
                throw new \RuntimeException('El Codigo Interno ya está en uso.');
            }


            $existingNombre = $this->entityManager->getRepository(TipoUsuario::class)
                ->findOneBy(['nombre' => $tipoUsuarioDTO->getNombre()]);


            if ($existingNombre) {
                throw new \RuntimeException('El Nombre ya está en uso.');
            }

            $excludePropertyName = ['fecha_creacion'];
            $this->updateEntityFromDTO($tipoUsuario, $tipoUsuarioDTO, $excludePropertyName);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al actualizar usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function deleteTipoUsuario(int $id): void
    {
        try {
            $this->markAsDeleted($id, 0);
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al eliminar usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }
}
