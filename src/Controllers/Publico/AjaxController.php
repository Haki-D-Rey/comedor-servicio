<?php

namespace App\Controllers\Publico;

use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;
use Slim\Psr7\Request;

class AjaxController
{
    private ContainerInterface $container;
    private EntityManager $entityManager;

    public function __construct(
        ContainerInterface $container,
        EntityManager $entityManager
    ) {
        $this->container = $container;
        $this->entityManager = $entityManager;
    }

    public function postAjaxServerSide(Request $request, Response $response): Response
    {
        try {
            // Obtener los parámetros del cuerpo de la solicitud
            $data = json_decode(file_get_contents("php://input"), true);

            // Obtener los parámetros que vienen en el cuerpo
            $tabla = isset($data['tabla']) ? $data['tabla'] : 'public.cliente';
            $columns_table = isset($data['columns_table']) ? $data['columns_table'] : ['*'];
            $column_dtrow = isset($data['column_dtrow']) ? $data['column_dtrow'] : [''];
            $filters = isset($data['filters']) ? $data['filters'] : [''];

            $searchValue = isset($data['searchValue']) ? $data['searchValue'] : '';
            $orderColumn = isset($data['orderColumn']) ? $data['columns'][$data['orderColumn']]['data'] : 'id';
            $orderDirection = isset($data['orderDirection']) ? $data['orderDirection'] : 'asc';
            $start = isset($data['start']) ? $data['start'] : 0;
            $length = isset($data['length']) ? $data['length'] : 10;

            $conn = $this->entityManager->getConnection();
            // Ejecutar la consulta para obtener los datos
            $clientes = $this->ejecutarQuery($conn, $tabla, $columns_table, $searchValue, $orderColumn, $orderDirection, $start, $length, $filters, $column_dtrow);

            if (isset($clientes) && is_array($clientes)) {

                $counter = $start + 1;

                foreach ($clientes as $key => &$cliente) {
                    $cliente['dt_rowid'] = $counter;
                    $counter++;
                    foreach ($cliente as $key => &$value) {
                        if (strpos(strtolower($key), 'fecha') !== false) {
                            $date = strtotime($value);
                            if ($date !== false) {
                                $value = date('Y-m-d', $date);
                            }
                        }
                    }
                }
            }

            $totalRecords = $this->obtenerTotalRegistros($conn, $tabla, $columns_table, $searchValue, $filters);
            $totalFiltered = $totalRecords;
            // Respuesta en formato JSON para DataTables
            $ajaxdata = array(
                "draw" => intval(isset($data['draw']) ? $data['draw'] : 1),
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $totalFiltered,
                "data" => $clientes
            );

            $response->getBody()->write(json_encode($ajaxdata));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }


    function generarQueryServerSide($tabla, $columns, $searchValue = '', $orderColumn = '', $orderDirection = 'asc', $start = 0, $length = 10, &$params, $filters, $column_dtrow)
    {
        $columnsString = [];
        $joins = [];
        $searchConditions = [];
        $whereConditions = [];
        $queryOrder = "";
        $tablaAlias = "TB";

        foreach ($columns as $column) {
            // $table = $column['tabla'];

            if (!empty($column['relational'])) {
                foreach ($column['relational'] as $relation) {

                    $tableName = $relation['name'];
                    $columnTable = $relation['column_table'];
                    $typeTableRelational = isset($relation['type']) && !empty($relation['type']) ? $relation['type'] : "INNER JOIN";

                    $joins[] = "$typeTableRelational $tableName ON $tablaAlias.{$column['name']} = $tableName.id"; // Asumimos que la relación usa la clave primaria 'id'
                    $column_name_table_padre = "{$column['name']}_{$columnTable}";
                    $columnsString[] = "$tableName.$columnTable AS $column_name_table_padre";

                    if (!empty($relation['filters'])) {
                        foreach ($relation['filters'] as $filter) {
                            $filterName = $filter['name'];
                            $filterValue = $filter['value'];

                            $paramName = "{$tableName}_{$filterName}";
                            $paramName = str_replace('.', '_', $paramName);

                            $whereConditions[] = "$tableName.$filterName = :$paramName";
                            $params[$paramName] = $filterValue;
                        }
                    }

                    if (!empty($searchValue)) {
                        $searchConditions[] = "LOWER($tableName.$columnTable) LIKE LOWER(:searchValue)";
                    }

                    if (!empty($orderColumn) && $orderColumn === $column_name_table_padre) {
                        $column_name_relational = $relation['name'] . '.' . $relation['column_table'];
                        $queryOrder = " ORDER BY $column_name_relational $orderDirection";
                    }
                }
            }
            $columnsString[] = "$tablaAlias.{$column['name']}";

            if (!empty($searchValue) && $column['searchable']) {
                $searchConditions[] = "LOWER(CAST($tablaAlias.{$column['name']} AS TEXT)) LIKE LOWER(:searchValue)";
            } elseif ($column['searchable'] && !empty($column['relational'])) {
                foreach ($column['relational'] as $relational) {
                    $column_name_relational = $relational['name'] . '.' . $relational['column_table'];
                    $searchConditions[] = "LOWER(CAST($column_name_relational AS TEXT)) LIKE LOWER(:searchValue)";
                }
            }

            if ($column['orderable'] && empty($column['relational'] && !empty($orderColumn)) && $orderColumn === $column['name']) {
                $queryOrder = " ORDER BY $tablaAlias.$orderColumn $orderDirection";
            }
        }

        foreach ($filters as $filter) {
            $key = strtolower($tablaAlias . '_' . $filter['name']);
            if ($filter['value'] === '' || $filter['value'] === null) {
                $filter['value'] = false;
            }
            $whereConditionsTable[] = "$tablaAlias.{$filter['name']} = :{$key}";
            $params[$key] = $filter['value'];
        }

        $columnsString = implode(", ", $columnsString);
        // $stringQueryDTRow = "";
        // if (!empty($column_dtrow)) {
        //     $stringQueryDTRow = "ROW_NUMBER() OVER (ORDER BY $tablaAlias.$column_dtrow asc) AS DT_RowId, ";
        // }

        $query = "SELECT $columnsString FROM $tabla AS $tablaAlias " . implode(" ", $joins) . " WHERE TRUE";

        // Agregar condiciones WHERE específicas de la tabla
        if (!empty($whereConditionsTable)) {
            $query .= " AND (" . implode(" AND ", $whereConditionsTable) . ")";
        }

        if (!empty($whereConditions)) {
            $query .= " AND (" . implode(" AND ", $whereConditions) . ")";
        }

        if (!empty($searchValue) && !in_array('*', array_column($columns, 'name'))) {
            $query .= " AND (" . implode(" OR ", $searchConditions) . ")";
        }

        if (!empty($queryOrder)) {
            $query .= " $queryOrder";
        }

        $query .= " LIMIT :length OFFSET :start";

        return $query;
    }

    function obtenerTotalRegistros($conn, $tabla, $columns, $searchValue, $filters)
    {
        // Inicializamos las variables
        $columnsString = [];
        $joins = [];
        $searchConditions = [];
        $whereConditions = [];
        $whereConditionsTable = []; // Inicializamos el array de condiciones adicionales de la tabla
        $params = [];

        $tablaAlias = "TB"; // Alias para la tabla principal

        // Construcción de las columnas y uniones (JOINs)
        foreach ($columns as $column) {
            // Si la columna tiene una relación
            if (!empty($column['relational'])) {
                foreach ($column['relational'] as $relation) {
                    $tableName = $relation['name'];
                    $columnTable = $relation['column_table'];
                    $typeTableRelational = isset($relation['type']) && !empty($relation['type']) ? $relation['type'] : "INNER JOIN";

                    // Crear el JOIN con la tabla relacionada
                    $joins[] = "$typeTableRelational $tableName ON $tablaAlias.{$column['name']} = $tableName.id";

                    // Añadir los filtros a la relación si existen
                    if (!empty($relation['filters'])) {
                        foreach ($relation['filters'] as $filter) {
                            $filterName = $filter['name'];
                            $filterValue = $filter['value'];

                            // Usamos el nombre de la tabla y el nombre del filtro para generar el nombre de parámetro
                            $paramName = "{$tableName}_{$filterName}";
                            $paramName = str_replace('.', '_', $paramName);  // Reemplazar el punto por guion bajo

                            // Agregar el filtro correspondiente a la cláusula WHERE
                            $whereConditions[] = "$tableName.$filterName = :$paramName";

                            // Guardar el filtro en los parámetros
                            $params[$paramName] = $filterValue;
                        }
                    }
                }
            }
        }

        // Filtrar por filtros adicionales de la tabla principal
        if (!empty($filters)) {
            foreach ($filters as $filter) {
                $key = 'tb' . '_' . $filter['name'];
                $whereConditionsTable[] = "$tablaAlias.{$filter['name']} = :{$key}";
                $params[$key] = $filter['value'];
            }
        }

        // Contamos los registros en la tabla principal
        $totalQuery = "SELECT COUNT(1) FROM $tabla AS $tablaAlias " . implode(" ", $joins) . " WHERE TRUE";

        // Si hay un valor de búsqueda global, agregar condición para la búsqueda
        if (!empty($searchValue) && !in_array('*', array_column($columns, 'name'))) {
            foreach ($columns as $column) {
                $name = $column['name'];
                if ($column['searchable'] && empty($column['relational'])) {
                    $searchConditions[] = "LOWER(CAST($tablaAlias.$name AS TEXT)) LIKE LOWER(:searchValue)";
                } elseif ($column['searchable'] && !empty($column['relational'])) {
                    foreach ($column['relational'] as $relational) {
                        $column_name_relational = $relational['name'] . '.' . $relational['column_table'];
                        $searchConditions[] = "LOWER(CAST($column_name_relational AS TEXT)) LIKE LOWER(:searchValue)";
                    }
                }
            }

            // Agregar la condición de búsqueda a la consulta
            $totalQuery .= " AND (" . implode(" OR ", $searchConditions) . ")";
            $params['searchValue'] = "%" . $searchValue . "%";
        }

        // Agregar filtros a las relaciones
        if (!empty($whereConditions)) {
            $totalQuery .= " AND (" . implode(" AND ", $whereConditions) . ")";
        }

        // Agregar filtros adicionales de la tabla principal
        if (!empty($whereConditionsTable)) {
            $totalQuery .= " AND (" . implode(" AND ", $whereConditionsTable) . ")";
        }

        // Aquí transformamos los parámetros para asegurar que los valores estén correctos
        $validatedParams = [];

        // Iterar sobre el array de $params y validar
        foreach ($params as $key => $value) {
            // Validar tipo booleano
            if (is_bool($value)) {
                $validatedParams[$key] = (bool) $value;
            }
            // Validar tipo entero
            elseif (is_int($value)) {
                $validatedParams[$key] = (int) $value;
            }
            // Validar tipo cadena
            elseif (is_string($value)) {
                $validatedParams[$key] = (string) $value;
            } else {
                $validatedParams[$key] = $value;
            }
        }

        // Ejecutar la consulta de conteo de registros
        $result = $conn->executeQuery($totalQuery, $validatedParams);

        // Retornar el total de registros
        return $result->fetchOne();
    }



    function ejecutarQuery($conn, $tabla, $columns, $searchValue, $orderColumn, $orderDirection, $start, $length, $filters, $column_dtrow)
    {
        $params = [];
        // Generar la consulta SQL
        $query = $this->generarQueryServerSide($tabla, $columns, $searchValue, $orderColumn, $orderDirection, $start, $length, $params, $filters, $column_dtrow);

        // Si no hay un asterisco en las columnas y hay un valor de búsqueda, agregamos el filtro de búsqueda
        if (!in_array('*', $columns) && !empty($searchValue)) {
            $params['searchValue'] = "%" . $searchValue . "%";
        }

        // Inicializamos los parámetros de longitud y desplazamiento
        $params['length'] = $length;
        $params['start'] = $start;

        // Aquí transformamos los parámetros para asegurar que los valores estén correctos
        $validatedParams = [];

        // Iterar sobre el array de $params y validar
        foreach ($params as $key => $value) {
            // Validar tipo booleano
            if (is_bool($value)) {
                $validatedParams[$key] = (bool) $value;
            }
            // Validar tipo entero
            elseif (is_int($value)) {
                $validatedParams[$key] = (int) $value;
            }
            // Validar tipo cadena
            elseif (is_string($value)) {
                $validatedParams[$key] = (string) $value;
            } else {
                $validatedParams[$key] = $value;
            }
        }

        // Ejecutar la consulta con los parámetros validados
        $result = $conn->executeQuery($query, $validatedParams);

        // Retornar los resultados de la consulta
        return $result->fetchAllAssociative();
    }
}
