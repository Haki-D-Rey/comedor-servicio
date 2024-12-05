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
            $usuario->getEstado()
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

    public function getEstadisticosPanel(int $id): array
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
            $usuario->getEstado()
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

    public function createZonaUsuario(array $zonaUsuarioDTOs): void
    {
        $batchSize = 20;
        $i = 0;
        $valuesInsert = [];
        $paramsInsert = [];
        $updateCasesCodigoInterno = [];
        $updateCasesFechaModificacion = [];
        $updateCasesEstado = [];
        $paramsUpdate = [];
        $idsToUpdate = [];
        $codigo_interno = null;

        $this->entityManager->beginTransaction();

        try {
            foreach ($zonaUsuarioDTOs as $zonaUsuarioDTO) {
                // Buscar zona y usuario
                $zona = $this->entityManager->getRepository(Zona::class)->findOneBy(['id' => $zonaUsuarioDTO->getIdZona()]);
                $usuario = $this->entityManager->getRepository(Usuario::class)->findOneBy(['id' => $zonaUsuarioDTO->getIdUsuario()]);

                if (!$zona) {
                    throw new \RuntimeException("Zona con ID {$zonaUsuarioDTO->getIdZona()} no encontrada.");
                }
                if (!$usuario) {
                    throw new \RuntimeException("Usuario con ID {$zonaUsuarioDTO->getIdUsuario()} no encontrado.");
                }

                // Verificar si ya existe una relación Zona-Usuario
                $existingRecord = $this->entityManager->getRepository(ZonaUsuarios::class)
                    ->findOneBy([
                        'zona' => $zona,
                        'usuario' => $usuario
                    ]);

                if ($existingRecord) {
                    // Si existe, agregar a las actualizaciones
                    $this->prepareUpdateValues($i, $zonaUsuarioDTO, $existingRecord, $updateCasesCodigoInterno, $updateCasesFechaModificacion, $updateCasesEstado, $paramsUpdate, $idsToUpdate);
                } else {
                    // Si no existe, agregar a las inserciones
                    $this->prepareInsertValues($zonaUsuarioDTO, $i, $paramsInsert, $valuesInsert, $codigo_interno);
                }

                // Control de batch: cada 20 registros se hace la inserción o actualización
                if (($i + 1) % $batchSize === 0) {
                    if (!empty($valuesInsert)) {
                        $this->insertZonaUsuario($valuesInsert, $paramsInsert);
                    }

                    if (!empty($paramsUpdate)) {
                        $this->updateZonaUsuarioData($paramsUpdate, $updateCasesCodigoInterno, $updateCasesFechaModificacion, $updateCasesEstado, $idsToUpdate);
                    }

                    // Reset para el siguiente batch
                    $valuesInsert = [];
                    $paramsInsert = [];
                    $idsToUpdate = [];
                }

                ++$i;
            }

            // Realizar las inserciones y actualizaciones restantes si hay registros pendientes
            if (!empty($valuesInsert)) {
                $this->insertZonaUsuario($valuesInsert, $paramsInsert);
            }

            if (!empty($paramsUpdate)) {
                $this->updateZonaUsuarioData($paramsUpdate, $updateCasesCodigoInterno, $updateCasesFechaModificacion, $updateCasesEstado, $idsToUpdate);
            }

            // Commit la transacción
            $this->entityManager->commit();
        } catch (\RuntimeException $e) {
            $this->entityManager->rollback();
            $this->logger->error('Error al crear la relación Zona Usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        } catch (ORMException | DBALException $e) {
            $this->entityManager->rollback();
            $this->logger->error('Error al crear la relación Zona Usuario en la base de datos: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al crear la relación Zona Usuario.');
        } catch (\Throwable $e) {
            $this->entityManager->rollback();
            $this->logger->error('Error inesperado al crear la relación Zona Usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    private function prepareInsertValues($zonaUsuarioDTO, $i, &$paramsInsert, &$valuesInsert, &$codigo_interno)
    {
        if ($codigo_interno == null) {
            $codigo_interno = $this->generateInternalCode('ZU-', 3, 0, ZonaUsuarios::class);
        } else {
            $codigo_interno = $this->incrementCode($codigo_interno, 3);
        }
        $valuesInsert[] = '(:id_zona' . $i . ', :id_usuario' . $i . ', :codigo_interno' . $i . ', :fecha_creacion' . $i . ', :fecha_modificacion' . $i . ', :estado' . $i . ')';
        $paramsInsert['id_zona' . $i] = $zonaUsuarioDTO->getIdZona();
        $paramsInsert['id_usuario' . $i] = $zonaUsuarioDTO->getIdUsuario();
        $paramsInsert['codigo_interno' . $i] = $codigo_interno;
        $paramsInsert['fecha_creacion' . $i] = $zonaUsuarioDTO->getFechaCreacion()->format('Y-m-d H:i:s');
        $paramsInsert['fecha_modificacion' . $i] = $zonaUsuarioDTO->getFechaModificacion() ? $zonaUsuarioDTO->getFechaModificacion()->format('Y-m-d H:i:s') : null;
        $paramsInsert['estado' . $i] = $zonaUsuarioDTO->getEstado();
    }

    private function prepareUpdateValues($i, $zonaUsuarioDTO, $existingRecord, &$updateCasesCodigoInterno, &$updateCasesFechaModificacion, &$updateCasesEstado, &$paramsUpdate, &$idsToUpdate)
    {
        // Preparar los casos de actualización
        $updateCasesCodigoInterno[] = "WHEN id = :id_zona_usuario" . $i . " THEN :codigo_interno" . $i;
        $updateCasesFechaModificacion[] = "WHEN id = :id_zona_usuario" . $i . " THEN :fecha_modificacion" . $i;
        $updateCasesEstado[] = "WHEN id = :id_zona_usuario" . $i . " THEN :estado" . $i;

        // Agregar parámetros para la actualización
        $paramsUpdate["id_zona_usuario" . $i] = $existingRecord->getId();
        $paramsUpdate["codigo_interno" . $i] =  $existingRecord->getCodigoInterno();
        $paramsUpdate["fecha_modificacion" . $i] = (new \DateTime())->format('Y-m-d H:i:s');
        $paramsUpdate["estado" . $i] = $zonaUsuarioDTO->getEstado();

        // Agregar el id a la lista de ids a actualizar
        $idsToUpdate[] = $existingRecord->getId();
    }

    private function insertZonaUsuario(array $valuesInsert, array $paramsInsert): void
    {
        $sql = "INSERT INTO seguridad.zona_usuario (id_zona, id_usuario, codigo_interno, fecha_creacion, fecha_modificacion, estado) 
                VALUES " . implode(', ', $valuesInsert);
        $this->entityManager->getConnection()->executeQuery($sql, $paramsInsert);
    }

    private function updateZonaUsuarioData($paramsUpdate, $updateCasesCodigoInterno, $updateCasesFechaModificacion, $updateCasesEstado, $idsToUpdate): void
    {
        $whereClause = "WHERE id IN (" . implode(', ', $idsToUpdate) . ")";

        $sqlUpdateFinal = "UPDATE seguridad.zona_usuario SET 
            codigo_interno = CASE " . rtrim(implode(' ', $updateCasesCodigoInterno), ', ') . " END,
            fecha_modificacion = CASE " . rtrim(implode(' ', $updateCasesFechaModificacion), ', ') . " END::TIMESTAMP,
            estado = CASE " . rtrim(implode(' ', $updateCasesEstado), ', ') . " END::BOOLEAN " . $whereClause;

        $this->entityManager->getConnection()->executeQuery($sqlUpdateFinal, $paramsUpdate);
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
