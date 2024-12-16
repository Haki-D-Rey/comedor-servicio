<?php

namespace App\Services\Utils;

use stdClass;

class UtilServices
{
    public function __construct() {}

    public static function transformArray(array $devices, array $template, bool $groupByDevice = false, array $groupFields = []): array
    {
        if ($groupByDevice) {
            return self::groupAndTransform($devices, $template, $groupFields);
        } else {
            return array_map(function ($device) use ($template) {
                return self::transformItem($device, $template);
            }, $devices);
        }
    }

    private static function groupAndTransform(array $devices, array $template, array $groupFields): array
    {
        $grouped = [];

        foreach ($devices as $device) {
            // Convertir a objeto si el dispositivo es un array
            if (is_array($device)) {
                $device = json_decode(json_encode($device)); // Cast array to object
            }

            // Construir los valores de agrupación dinámicamente según los campos proporcionados
            $groupValues = [];
            foreach ($groupFields as $field) {
                // Verificar si el campo existe en el template y si la propiedad existe en el dispositivo
                if (isset($template[$field]) && property_exists($device, $template[$field])) {
                    $groupValues[] = $device->{$template[$field]};  // Acceso a la propiedad del objeto
                } else {
                    // Si no existe, agregar valor nulo o lo que desees
                    $groupValues[] = null;
                }
            }

            // Crear una clave compuesta de los valores de agrupación
            $groupKey = implode('|', $groupValues);

            // Inicializar el grupo si no existe
            if (!isset($grouped[$groupKey])) {
                $grouped[$groupKey] = self::initializeGroupedItem($device, $template, $groupFields);
            }

            // Agregar los datos del dispositivo al grupo
            $dataToAdd = [];
            foreach ($template as $key => $field) {
                if (!in_array($key, $groupFields) && property_exists($device, $field)) {
                    $dataToAdd[$key] = $device->{$field};  // Acceso a la propiedad del objeto
                }
            }

            // Agrupar los datos por fecha_emision u otros campos dinámicos
            $fechaEmision = $device->{$template['fecha_emision']};

            // Si no existe la fecha_emision, inicializarla
            if (!isset($grouped[$groupKey][$fechaEmision])) {
                $grouped[$groupKey][$fechaEmision] = [];
            }

            // Agregar los datos agrupados a la fecha correspondiente
            $grouped[$groupKey][$fechaEmision][] = $dataToAdd;
        }

        // Ajustar el formato final para que los resultados estén como un array de objetos
        $finalResult = [];
        foreach ($grouped as $groupKey => $group) {
            // Obtener el valor de los campos de agrupación (por ejemplo, id_zona y fecha_emision)
            $keys = explode('|', $groupKey);
            $groupItem = [];

            // Añadir los grupos de fecha_emision y sus datos correspondientes
            foreach ($group as $fecha => $data) {
                $groupItem[$fecha] = $data;
            }

            // Asignar la clave del primer campo de agrupación como id_zona
            $groupItem['id_zona'] = $keys[0];

            // **Eliminamos el campo fecha_emision fuera del agrupado**

            // Añadir el grupo al resultado final
            $finalResult[] = $groupItem;
        }

        return $finalResult;
    }

    private static function initializeGroupedItem(stdClass $device, array $template, array $groupFields): array
    {
        $result = [];

        // Inicializar la estructura para cada grupo
        foreach ($template as $key => $value) {
            if (in_array($key, $groupFields)) {
                // Si el campo es parte de los campos de agrupación, lo agregamos a la clave del grupo
                $result[$key] = $device->{$value};
            } elseif (is_array($value)) {
                $result[$key] = [];
            } elseif (property_exists($device, $value)) {
                $result[$key] = $device->{$value};
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    private static function transformItem(stdClass $device, array $template): array
    {
        $result = [];

        // Transformar un solo elemento
        foreach ($template as $key => $value) {
            if (is_array($value)) {
                $result[$key] = self::transformItem($device, $value);
            } elseif (property_exists($device, $value)) {
                $result[$key] = $device->{$value};
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
