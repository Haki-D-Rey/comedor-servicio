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

            // Definir los campos a actualizar desde el DTO
            $fieldsToUpdate = [];

            // Aquí asignamos cada atributo del DTO al campo correspondiente
            if ($clientesDTO->getNombres()) {
                $nombresFormatted = strtoupper(trim($clientesDTO->getNombres())); // Convertir a mayúsculas y eliminar espacios
                $fieldsToUpdate[] = ['field' => 'nombres', 'value' => $nombresFormatted];
            }
            if ($clientesDTO->getApellidos()) {
                $apellidosFormatted = strtoupper(trim($clientesDTO->getApellidos())); // Convertir a mayúsculas y eliminar espacios
                $fieldsToUpdate[] = ['field' => 'apellidos', 'value' => $apellidosFormatted];
            }
            if ($clientesDTO->getIdDepartamento()) {
                $fieldsToUpdate[] = ['field' => 'id_departamento', 'value' => $clientesDTO->getIdDepartamento()];
            }
            if ($clientesDTO->getIdCargo()) {
                $fieldsToUpdate[] = ['field' => 'id_cargo', 'value' => $clientesDTO->getIdCargo()];
            }
            if ($clientesDTO->getCorreo()) {
                $correoFormatted = strtolower(trim($clientesDTO->getCorreo())); // Convertir a minúsculas y eliminar espacios
                $fieldsToUpdate[] = ['field' => 'correo', 'value' => $correoFormatted];
            }
            if ($clientesDTO->getClieDocnum()) {
                $clieDocnumFormatted = str_pad($clientesDTO->getClieDocnum(), 10, '0', STR_PAD_LEFT);
                $fieldsToUpdate[] = ['field' => 'clie_docnum', 'value' => $clieDocnumFormatted];
            }
            if ($cliente->getFechaCreacion()) {
                $fieldsToUpdate[] = ['field' => 'fecha_creacion', 'value' => $cliente->getFechaCreacion()->format('Y-m-d H:i:s')];
            }
            if ($clientesDTO->getFechaModificacion()) {
                $fieldsToUpdate[] = ['field' => 'fecha_modificacion', 'value' => $clientesDTO->getFechaModificacion()->format('Y-m-d H:i:s')];
            }
            if ($clientesDTO->getEstado() !== null) {  // Asegurarse de que no sea un valor nulo
                $fieldsToUpdate[] = ['field' => 'estado', 'value' => $clientesDTO->getEstado()];
            }

            // Llamar al método markAsUpdateMethod para actualizar los campos
            $this->markAsUpdateMethod('public.cliente', $fieldsToUpdate, $id);


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
            // Pasar los campos a actualizar (ejemplo)
            $fields = [
                ['field' => 'estado', 'value' => 0]
            ];

            // Llamar al método para marcar como eliminado
            $this->markAsUpdateMethod('public.cliente', $fields, $id);
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al eliminar cliente: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function getSearchClients(array $filtro): array
    {
        try {
            $nombreBusqueda = strtolower(trim($filtro['nombres'] ?? ''));
            $apellidoBusqueda = strtolower(trim($filtro['apellidos'] ?? ''));

            // Crear QueryBuilder
            $qb = $this->entityManager->createQueryBuilder();

            // Construir la consulta
            $qb->select('c')
                ->from(Clientes::class, 'c')
                ->where('c.estado = true'); // Condición base para agregar dinámicamente filtros

            if (!empty($apellidoBusqueda)) {
                $qb->andWhere('LOWER(c.apellidos) LIKE :apellidoBusqueda')
                    ->setParameter('apellidoBusqueda', '%' . $apellidoBusqueda . '%');
            }

            if (!empty($nombreBusqueda)) {
                $qb->andWhere('LOWER(c.nombres) LIKE :nombreBusqueda')
                    ->setParameter('nombreBusqueda', '%' . $nombreBusqueda . '%');
            }

            // Ejecutar la consulta y devolver los resultados
            return $qb->getQuery()->getResult();
        } catch (\Exception $e) {
            $this->logger->error('Error en la búsqueda de clientes: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al buscar clientes: ' . $e->getMessage());
        }
    }

    public function getClientsRelationalIdentification(array $filtros): array
    {
        try {
            $id_cliente = $filtros['id_cliente'];
            $id_detalle_zona_servicio_horario = $filtros['id_detalle_zona_servicio_horario'];
            // // Crear QueryBuilder
            // $qb = $this->entityManager->getRepository(DetalleClienteIdentificacionFacturacion::class)->createQueryBuilder('c');

            // // Construir la consulta
            // $qb->innerJoin(DetalleZonaServicioHorarioClienteFacturacion::class, 'dzsci', 'c.cliente = dzsci.DetalleClienteIdentificacionFacturacion')
            //     ->where('c.estado = true'); // Condición base para agregar dinámicamente filtros

            // if (!empty($id_cliente)) {
            //     $qb->andWhere('c.cliente = :id_cliente')
            //         ->setParameter('id_cliente', $id_cliente);
            // }

            // if (!empty($id_detalle_zona_servicio_horario)) {
            //     $qb->andWhere('dzsci.detalleZonaServicioHorario = :id_detalle_zona_servicio_horario')
            //         ->setParameter('id_detalle_zona_servicio_horario', $id_detalle_zona_servicio_horario);
            // }

            // Ejecutar la consulta y devolver los resultados
            // return $qb->getQuery()->getResult();


            // 2. Ejecutar la segunda consulta SQL (contar las ventas)
            $query = "SELECT dzsci.*, c.json_identificacion
                    FROM public.detalle_cliente_identificacion_facturacion AS c
                    INNER JOIN public.detalle_zona_servicio_horario_cliente_facturacion AS dzsci
                        ON dzsci.id_detalle_cliente_identificacion_facturacion = c.id
                    WHERE c.id_cliente = :id_cliente
                        AND dzsci.id_detalle_zona_servicio_horario = :id_detalle_zona_servicio_horario
                        AND dzsci.estado = true AND c.estado = true";

            $params = [
                'id_cliente' => $id_cliente, // Cambié estos valores a los que se obtienen del DTO
                'id_detalle_zona_servicio_horario' => $id_detalle_zona_servicio_horario
            ];

            $result = $this->entityManager->getConnection()->executeQuery($query, $params);
            return $result->fetchAllAssociative();
        } catch (\Exception $e) {
            $this->logger->error('Error en la búsqueda de clientes: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al buscar clientes: ' . $e->getMessage());
        }
    }


    public function getValidateFormById($row): array
    {
        try {
            $data = $row[0];
            $validationResult = $this->validateUniqueFields($data);
            return $validationResult;
        } catch (\Exception $e) {
            $this->logger->error('Error en la búsqueda de clientes: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al buscar clientes: ' . $e->getMessage());
        }
    }
}
