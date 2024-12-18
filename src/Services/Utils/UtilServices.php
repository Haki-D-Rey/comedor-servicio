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
            if (is_array($device)) {
                $device = json_decode(json_encode($device)); // Convertir array a objeto
            }
    
            // Construir los valores de agrupación dinámicamente basados en los campos definidos en el template
            $groupValues = [];
            foreach ($groupFields as $field) {
                if (isset($template[$field]) && property_exists($device, $template[$field])) {
                    $groupValues[] = $device->{$template[$field]};
                } else {
                    $groupValues[] = null;
                }
            }
    
            // Crear una clave compuesta para la agrupación
            $groupKey = implode('|', $groupValues);
    
            // Inicializar el grupo si no existe
            if (!isset($grouped[$groupKey])) {
                $grouped[$groupKey] = [];
    
                // Llenar los campos dinámicos para la agrupación inicial
                foreach ($groupFields as $field) {
                    $grouped[$groupKey][$field] = $device->{$template[$field]} ?? null;
                }
    
                // Agregar un campo de agrupación dinámica
                $grouped[$groupKey]['data'] = [];
            }
    
            // Preparar los datos para el campo "data" (excluyendo campos de agrupación)
            $dataItem = [];
            foreach ($template as $key => $field) {
                if (!in_array($key, $groupFields) && property_exists($device, $field)) {
                    $dataItem[$key] = $device->{$field};
                }
            }
    
            // Agregar el elemento transformado al campo "data"
            $grouped[$groupKey]['data'][] = $dataItem;
        }
    
        // Convertir a un array indexado
        return array_values($grouped);
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
