<?php

namespace App\Repository\Publico\Repository;

use App\DTO\Publico\ClientesDTO;
use App\Entity\ListaCatalogo;
use App\Entity\ListaCatalogoDetalle;
use App\Entity\Publico\Clientes;
use App\Repository\Publico\Interface\ClientesRepositoryInterface;
use App\Repository\GenericRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface as LogLoggerInterface;

class ClientesRepository extends GenericRepository implements ClientesRepositoryInterface
{
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LogLoggerInterface $loggerInterface)
    {
        parent::__construct($entityManager, Clientes::class);
        $this->logger = $loggerInterface;
    }

    public function getAllClientes(): array
    {
        try {
            $clientes = $this->getAllEntities();
            return array_map(function (Clientes $cliente) {
                return new ClientesDTO(
                    $cliente->getId(),
                    $cliente->getNombres(),
                    $cliente->getApellidos(),
                    $cliente->getDepartamento()->getId(),
                    $cliente->getCargo()->getId(),
                    $cliente->getCorreo(),
                    $cliente->getClieDocnum(),
                    $cliente->getFechaCreacion(),
                    $cliente->getFechaModificacion(),
                    $cliente->getEstado()
                );
            }, $clientes);
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener clientes: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function getClienteById(int $id): ?ClientesDTO
    {
        try {
            $cliente = $this->getEntityById($id);
            if (!$cliente) {
                return null;
            }

            return new ClientesDTO(
                $cliente->getId(),
                $cliente->getNombres(),
                $cliente->getApellidos(),
                $cliente->getDepartamento()->getId(),
                $cliente->getCargo()->getId(),
                $cliente->getCorreo(),
                $cliente->getClieDocnum(),
                $cliente->getFechaCreacion(),
                $cliente->getFechaModificacion(),
                $cliente->getEstado()
            );
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener cliente por ID: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function createCliente(array $clientesDTOs): void
    {
        $this->entityManager->beginTransaction();

        try {

            $listaCatalogoRepository = $this->entityManager->getRepository(ListaCatalogo::class);
            $listaCatalogo = $listaCatalogoRepository->findBy([
                'codigo_interno' => ['LCU-001', 'LCU-002']
            ]);

            if (count($listaCatalogo) < 2) {
                throw new \Exception('No se encontraron los catálogos necesarios.');
            }

            $idDepartamentoCatalogo = $listaCatalogo[0]->getId();
            $idCargoCatalogo = $listaCatalogo[1]->getId();

            $correos = array_map(fn($clienteDTO) => $clienteDTO->getCorreo(), $clientesDTOs);
            $documentos = array_map(fn($clienteDTO) => $clienteDTO->getClieDocnum(), $clientesDTOs);
            $nombresCompletos = array_map(fn($clienteDTO) => $this->sanitizeString($clienteDTO->getNombres() . ' ' . $clienteDTO->getApellidos()), $clientesDTOs);

            $existingCorreos = $this->entityManager->getRepository(Clientes::class)
                ->findBy(['correo' => $correos]);

            $existingDocs = $this->entityManager->getRepository(Clientes::class)
                ->findBy(['clieDocnum' => $documentos]);

            $existingNombresCompletos = $this->entityManager->getRepository(Clientes::class)
                ->findBy(['nombres' => $nombresCompletos]);

            $clientesToPersist = [];

            foreach ($clientesDTOs as $clientesDTO) {

                $existingCorreo = array_filter($existingCorreos, fn($cliente) => $cliente->getCorreo() === $clientesDTO->getCorreo());
                if ($existingCorreo) {
                    throw new \RuntimeException("El correo {$clientesDTO->getCorreo()} ya está en uso.");
                }

                $existingDocnum = array_filter($existingDocs, fn($cliente) => $cliente->getClieDocnum() === $clientesDTO->getClieDocnum());
                if ($existingDocnum) {
                    throw new \RuntimeException("El número de documento {$clientesDTO->getClieDocnum()} ya está en uso.");
                }

                $clienteNombreCompletoSanitized = $this->sanitizeString($clientesDTO->getNombres() . ' ' . $clientesDTO->getApellidos());
                $existingNombreCompleto = array_filter($existingNombresCompletos, fn($cliente) => $this->sanitizeString($cliente->getNombres() . ' ' . $cliente->getApellidos()) === $clienteNombreCompletoSanitized);
                if ($existingNombreCompleto) {
                    throw new \RuntimeException("El nombre completo '{$clientesDTO->getNombres()} {$clientesDTO->getApellidos()}' ya está en uso.");
                }

                $departamento = $this->findMatchingDetalle($idDepartamentoCatalogo, $clientesDTO->getIdDepartamento(), ListaCatalogoDetalle::class);
                $cargo = $this->findMatchingDetalle($idCargoCatalogo, $clientesDTO->getIdCargo(), ListaCatalogoDetalle::class);

                if (!$departamento || !$cargo) {
                    throw new \Exception('Departamento o Cargo no encontrado.');
                }

                $cliente = new Clientes();
                $cliente->setNombres($clientesDTO->getNombres());
                $cliente->setApellidos($clientesDTO->getApellidos());
                $cliente->setDepartamento($departamento);
                $cliente->setCargo($cargo);
                $cliente->setCorreo($clientesDTO->getCorreo());
                $cliente->setClieDocnum($clientesDTO->getClieDocnum());
                $cliente->setFechaCreacion($clientesDTO->getFechaCreacion());
                $cliente->setFechaModificacion($clientesDTO->getFechaModificacion());
                $cliente->setEstado($clientesDTO->getEstado());

                $clientesToPersist[] = $cliente;
            }

            foreach ($clientesToPersist as $cliente) {
                $this->entityManager->persist($cliente);
            }

            $this->entityManager->flush();

            $this->entityManager->commit();
        } catch (ORMException | DBALException $e) {
            $this->entityManager->rollback();
            $this->logger->error('Error al crear clientes: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al crear clientes.');
        } catch (\Throwable $e) {
            $this->entityManager->rollback();
            $this->logger->error('Error inesperado al crear clientes: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }


    public function updateCliente(int $id, ClientesDTO $clientesDTO): void
    {
        $this->entityManager->beginTransaction();  // Iniciar la transacción
    
        try {
            // Obtener los catálogos necesarios por código interno
            $listaCatalogoRepository = $this->entityManager->getRepository(ListaCatalogo::class);
            $listaCatalogo = $listaCatalogoRepository->findBy([
                'codigo_interno' => ['LCU-001', 'LCU-002']
            ]);
    
            if (count($listaCatalogo) < 2) {
                throw new \Exception('No se encontraron los catálogos necesarios.');
            }
    
            $idDepartamentoCatalogo = $listaCatalogo[0]->getId(); // LCU-001
            $idCargoCatalogo = $listaCatalogo[1]->getId(); // LCU-002
    
            // Buscar el cliente por ID
            $cliente = $this->entityManager->getRepository(Clientes::class)->find($id);
            if (!$cliente) {
                throw new \Exception("Cliente con ID {$id} no encontrado.");
            }
    
            // Validar que no haya otro cliente con el mismo correo
            $existingCorreo = $this->entityManager->getRepository(Clientes::class)
                ->findOneBy(['correo' => $clientesDTO->getCorreo()]);
            if ($existingCorreo && $existingCorreo->getId() !== $cliente->getId()) {
                throw new \RuntimeException("El correo {$clientesDTO->getCorreo()} ya está en uso.");
            }
    
            // Validar que no haya otro cliente con el mismo número de documento
            $existingDocnum = $this->entityManager->getRepository(Clientes::class)
                ->findOneBy(['clieDocnum' => $clientesDTO->getClieDocnum()]);
            if ($existingDocnum && $existingDocnum->getId() !== $cliente->getId()) {
                throw new \RuntimeException("El número de documento {$clientesDTO->getClieDocnum()} ya está en uso.");
            }
    
            // Validar que no haya otro cliente con el mismo nombre completo
            $clienteNombreCompletoSanitized = $this->sanitizeString($clientesDTO->getNombres() . ' ' . $clientesDTO->getApellidos());
            $existingNombreCompleto = $this->entityManager->getRepository(Clientes::class)
                ->findOneBy(['nombres' => $clientesDTO->getNombres(), 'apellidos' => $clientesDTO->getApellidos()]);
            if ($existingNombreCompleto && $existingNombreCompleto->getId() !== $cliente->getId()) {
                throw new \RuntimeException("El nombre completo '{$clientesDTO->getNombres()} {$clientesDTO->getApellidos()}' ya está en uso.");
            }
    
            // Obtener el departamento y cargo correspondientes a los catálogos
            $departamento = $this->findMatchingDetalle($idDepartamentoCatalogo, $clientesDTO->getIdDepartamento(), ListaCatalogoDetalle::class);
            $cargo = $this->findMatchingDetalle($idCargoCatalogo, $clientesDTO->getIdCargo(), ListaCatalogoDetalle::class);
    
            if (!$departamento || !$cargo) {
                throw new \Exception('Departamento o Cargo no encontrado.');
            }
    
            // Excluir la propiedad fecha_creacion al actualizar
            $excludePropertyName = ['fecha_creacion'];
            $this->updateEntityFromDTO($cliente, $clientesDTO, $excludePropertyName);
    
            // Persistir los cambios en la base de datos
            $this->entityManager->flush();
    
            // Confirmar la transacción
            $this->entityManager->commit();
        } catch (ORMException | DBALException $e) {
            $this->entityManager->rollback();  // Deshacer cambios en caso de error
            $this->logger->error('Error al actualizar clientes: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al actualizar clientes.');
        } catch (\Throwable $e) {
            $this->entityManager->rollback();  // Deshacer cambios en caso de error
            $this->logger->error('Error inesperado al actualizar clientes: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }
    

    public function deleteCliente(int $id): void
    {
        try {
            $this->markAsDeleted($id, 0);
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al eliminar cliente: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }
}
