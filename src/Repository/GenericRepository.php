<?php

namespace App\Repository;

use App\Entity\ListaCatalogoDetalle;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use ReflectionClass;
use stdClass;

abstract class GenericRepository extends EntityRepository
{
    protected $entityManager;
    protected $repository;

    public function __construct(EntityManagerInterface $entityManager, string $entityClass)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository($entityClass);
        parent::__construct($entityManager, $entityManager->getClassMetadata($entityClass));
    }

    public function getAllEntities(): array
    {
        return $this->repository->findAll();
    }

    public function getEntityById(int $id)
    {
        return $this->repository->find($id);
    }

    protected function updateEntityFromDTO($entity, $dto, $excludedProperties): void
    {
        $entityReflection = new ReflectionClass($entity);
        $dtoReflection = new ReflectionClass($dto);

        foreach ($dtoReflection->getProperties() as $dtoProperty) {
            $dtoProperty->setAccessible(true);
            $value = $dtoProperty->getValue($dto);
            $propertyName = $dtoProperty->getName();

            if (in_array($propertyName, $excludedProperties)) {
                continue;
            }
            if ($propertyName === 'contrasenia') {
                $value = password_hash($value, PASSWORD_BCRYPT);
            }

            if ($value !== null) {
                $setterMethod = 'set' . ucfirst($propertyName);

                if ($entityReflection->hasMethod($setterMethod)) {
                    $entity->$setterMethod($value);
                }
            }
        }
    }

    /**
     * Marca un registro como "eliminado" en la base de datos.
     * 
     * Esta función actualiza los valores de los campos proporcionados
     * en la tabla indicada, donde el ID coincide con el valor dado.
     *
     * @param string $table Nombre de la tabla a actualizar.
     * @param array $fields Campos a actualizar.
     * @param mixed $value El valor a asignar a los campos especificados.
     * @param int $id ID del registro a actualizar.
     * 
     * @throws RuntimeException Si ocurre un error al ejecutar la consulta SQL.
     */
    public function markAsUpdateMethod(string $table, array $fields, int $id): void
    {
        // Validar que los parámetros sean correctos
        $this->validateParameters($table, $fields, $id);

        try {
            // Construir la parte SET de la consulta SQL dinámicamente
            $setParts = $this->buildSetPart($fields);

            // Incluir la fecha de modificación
            // $setParts[] = 'fecha_modificacion = NOW()';

            // Construir la consulta SQL de actualización
                $query = sprintf(
                'UPDATE %s SET %s WHERE id = ?',
                $table,
                implode(', ', $setParts['set'])
            );

            // Preparar los parámetros de la consulta, incluyendo el ID
            $params = array_merge($setParts['params'], [$id]);

            // Ejecutar la consulta con los parámetros
            $connection = $this->entityManager->getConnection();
            $connection->executeQuery($query, $params);
        } catch (DriverException | \Exception $e) {
            // Loguear y manejar excepciones
            // $this->logger->error('Error al actualizar el registro en la tabla ' . $table . ': ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al actualizar el registro en la tabla ' . $table . ': ' . $e->getMessage());
        }
    }

    public function findMatchingDetalle(int $id_lista_catalogo, int $idValor, string $entityClass): ?ListaCatalogoDetalle
    {
        // Query the entity repository with the provided parameters
        $queryBuilder = $this->entityManager->getRepository($entityClass)->createQueryBuilder('d');

        $queryBuilder->where('d.id_lista_catalogo = :id_lista_catalogo')
            ->andWhere('d.id_valor = :id_valor')
            ->andWhere('d.estado = :estado')
            ->setParameter('id_lista_catalogo', $id_lista_catalogo)
            ->setParameter('id_valor', $idValor)
            ->setParameter('estado', true)
            ->setMaxResults(1);  // Limita a un solo resultado

        // Obtener el resultado
        $result = $queryBuilder->getQuery()->getOneOrNullResult();

        return $result;
    }

    public function sanitizeString(string $string): string
    {
        return str_replace(' ', '_', trim($string));
    }

    public function generateInternalCode(string $prefix, int $leadingZeros, int $lastValue, string $table = null): string
    {
        if (!$lastValue && $table) {
            $lastValue = $this->getLastValueFromTable($table);
        }

        $newValue = $lastValue + 1;
        $formattedValue = str_pad($newValue, $leadingZeros, '0', STR_PAD_LEFT);
        $newInternalCode = $prefix . $formattedValue;

        return $newInternalCode;
    }

    private function getLastValueFromTable(string $table): int
    {
        $repository = $this->entityManager->getRepository($table);
        $queryBuilder = $repository->createQueryBuilder('e')
            ->select('MAX(e.id)')
            ->getQuery();

        $lastValue = $queryBuilder->getSingleScalarResult();
        return $lastValue !== null ? (int)$lastValue : 0;
    }

    public function incrementCode($codigo_interno, $leading_zero)
    {
        $pattern = '/(\D+)(\d+)$/';
        if (preg_match($pattern, $codigo_interno, $matches)) {
            $prefix = $matches[1];
            $number = (int)$matches[2];

            $number++;

            $new_code = $prefix . str_pad($number, $leading_zero, '0', STR_PAD_LEFT);
            return $new_code;
        }
        return $codigo_interno;
    }

    /**
     * Valida los parámetros antes de ejecutar la consulta SQL.
     * 
     * @param string $table Nombre de la tabla.
     * @param array $fields Campos a actualizar.
     * @param int $id ID del registro.
     *
     * @throws InvalidArgumentException Si algún parámetro es inválido.
     */
    private function validateParameters(string $table, array $fields, int $id): void
    {
        // Validar que el nombre de la tabla no esté vacío
        if (empty($table)) {
            throw new \InvalidArgumentException('El nombre de la tabla no puede estar vacío.');
        }

        // Validar que los campos no estén vacíos
        if (empty($fields)) {
            throw new \InvalidArgumentException('La lista de campos no puede estar vacía.');
        }

        // Validar que el ID sea un número entero positivo
        if ($id <= 0) {
            throw new \InvalidArgumentException('El ID debe ser un número entero positivo.');
        }
    }

    /**
     * Construye dinámicamente la parte SET de la consulta SQL.
     * 
     * @param array $fields Array con objetos que contienen el campo y su valor a actualizar.
     * 
     * @return array Contiene la parte SET de la consulta y los parámetros.
     */
    private function buildSetPart(array $fields): array
    {
        $setParts = [];
        $params = [];

        foreach ($fields as $fieldData) {
            if (!isset($fieldData['field']) || !isset($fieldData['value'])) {
                throw new \InvalidArgumentException('Cada elemento de "fields" debe tener las claves "field" y "value".');
            }

            $setParts[] = $fieldData['field'] . ' = ?';
            $params[] = $fieldData['value'];
        }

        return [
            'set' => $setParts,
            'params' => $params
        ];
    }


    /**
     * Valida si los campos especificados ya existen en otro registro, excluyendo el registro actual.
     * 
     * @param stdClass $json Datos que contienen los campos a validar y el id del registro.
     * 
     * @return array Un arreglo con el estado de la validación y el mensaje correspondiente.
     */
    public function validateUniqueFields(stdClass $json): array
    {
        // Extraer la tabla, los campos a validar y el id del registro
        $table = $json->table;
        $fields = $json->fields[0];  // Los campos a validar
        $id = $json->id;  // ID del registro actual

        // Construir las condiciones para la consulta SQL
        $conditions = [];
        $params = [];

        // Recorrer los campos a validar y construir las condiciones SQL
        foreach ($fields as $field => $value) {
            $conditions[] = "$field = ?";
            $params[] = $value;
        }

        // Excluir el registro que se está actualizando (por ID)
        $conditions[] = 'id != ?';
        $params[] = $id;

        // Construir la consulta SQL para verificar si existe algún registro con los mismos valores
        $query = sprintf(
            'SELECT COUNT(*) FROM %s WHERE %s',
            $table,
            implode(' AND ', $conditions)
        );

        // Ejecutar la consulta de validación
        $connection = $this->entityManager->getConnection();
        $count = $connection->fetchOne($query, $params);

        // Si se encontró algún registro, devolver error con los campos duplicados
        if ($count > 0) {
            return [
                'estado' => false,
                'message' => 'NO Puede actualizar la informacion verificada',
                'campos_duplicados' => array_keys($fields) // Devolver los campos duplicados
            ];
        }

        // Si no hay registros duplicados, devolver éxito
        return [
            'estado' => true,
            'message' => 'Puede actualizar la informacion verificada'
        ];
    }
}
