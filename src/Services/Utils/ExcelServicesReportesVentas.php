<?php

namespace App\Services\Utils;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelServicesReportesVentas
{
    public function setDocumentProperties(Spreadsheet $spreadsheet)
    {
        // Configurar propiedades del documento
        return $spreadsheet->getProperties()->setCreator("GTI")
            ->setLastModifiedBy("GTI")
            ->setTitle("Reporte de Ventas SURE")
            ->setSubject("Reporte de Ventas de Servicios por Eventos HMEADB")
            ->setCategory("Reporte Ventas SURE");
    }

    public function setHeaders(Worksheet $sheet)
    {
        return $sheet;
    }

    public function formData(array $data, Worksheet $sheet, int $fila): void
    {
        $currentRow = $fila;
        $totalServicios = [];
        $granTotal = 0;

        // 1. Agrupar y ordenar los servicios por nombre
        $serviciosOrdenados = [];
        foreach ($data as $item) {
            foreach ($item['data'] as $servicio) {
                // Organizar los servicios por nombre y evento
                $serviciosOrdenados[$servicio['nombre_servicio']][$servicio['evento']] = $servicio['order'];
            }
        }

        // Asegurarse de que los servicios estén ordenados correctamente
        ksort($serviciosOrdenados);

        // 2. Crear un mapa de columnas dinámico para cada servicio y evento
        $columnMap = [];
        $colIndex = ord('C');
        foreach ($serviciosOrdenados as $servicio => $eventos) {
            foreach ($eventos as $evento => $order) {
                $columnMap["$servicio - $evento"] = chr($colIndex);
                $colIndex++;
            }
        }

        // 3. Eliminar duplicados y ordenar las fechas
        $fechas = array_column($data, 'fecha_emision');
        $fechasUnicas = array_unique($fechas);  // Eliminar fechas duplicadas
        sort($fechasUnicas);  // Ordenar las fechas de forma ascendente

        // 4. ENCABEZADO PRINCIPAL: Evento
        $sheet->mergeCells("B$currentRow:" . chr($colIndex - 1) . "$currentRow");
        $sheet->getStyle("B$currentRow:" . chr($colIndex - 1) . "$currentRow")->getFont()->setBold(true);
        $sheet->getStyle("B$currentRow:" . chr($colIndex - 1) . "$currentRow")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $currentRow++;

        // 5. Encabezados dinámicos en base al orden de los servicios y eventos
        $sheet->setCellValue('B' . $currentRow, 'FECHA');
        foreach ($columnMap as $servicioEvento => $col) {
            $sheet->setCellValue($col . $currentRow, $servicioEvento); // Nombre del servicio + evento
        }
        $sheet->getStyle("B$currentRow:" . chr($colIndex - 1) . "$currentRow")->getFont()->setBold(true);
        $currentRow++;

        // 6. Datos dinámicos (consolidado por evento)
        foreach ($fechasUnicas as $fecha) {
            $servicioTotales = array_fill_keys(array_values($columnMap), 0); // Inicializar totales por servicio con 0

            foreach ($data as $item) {
                if ($item['fecha_emision'] === $fecha) {
                    foreach ($item['data'] as $plan) {
                        if (isset($columnMap["{$plan['nombre_servicio']} - {$plan['evento']}"])) {
                            $col = $columnMap["{$plan['nombre_servicio']} - {$plan['evento']}"];
                            $servicioTotales[$col] += $plan['cantidad_total_facturada']; // Sumar la cantidad facturada
                        }
                    }
                }
            }

            // Escribir fila de datos
            $sheet->setCellValue('B' . $currentRow, $fecha);
            foreach ($servicioTotales as $col => $cantidad) {
                // Asegurar que los valores vacíos sean 0
                $sheet->setCellValue($col . $currentRow, $cantidad > 0 ? $cantidad : 0); // Escribir 0 si no hay cantidad
            }

            // Acumular totales generales
            foreach ($servicioTotales as $col => $cantidad) {
                $totalServicios[$col] = ($totalServicios[$col] ?? 0) + $cantidad;
                $granTotal += $cantidad;
            }
            $currentRow++;
        }

        // 7. Totales por servicio
        $sheet->setCellValue('B' . $currentRow, 'TOTAL SERVICIOS');
        foreach ($totalServicios as $col => $total) {
            $sheet->setCellValue($col . $currentRow, $total);
        }
        $sheet->getStyle("B$currentRow:" . chr($colIndex - 1) . "$currentRow")->getFont()->setBold(true);
        $currentRow++;

        // 8. Total cierre
        $sheet->mergeCells("B$currentRow:" . chr($colIndex - 2) . "$currentRow");
        $sheet->setCellValue('B' . $currentRow, 'TOTAL CIERRE');
        $sheet->setCellValue(chr($colIndex - 1) . $currentRow, $granTotal);
        $sheet->getStyle("B$currentRow:" . chr($colIndex - 1) . "$currentRow")->getFont()->setBold(true);
        $sheet->getStyle("B$currentRow:" . chr($colIndex - 1) . "$currentRow")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Aplicar bordes y ajustar ancho automático
        $sheet->getStyle("B$fila:" . chr($colIndex - 1) . "$currentRow")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        foreach (range('B', chr($colIndex - 1)) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->setCellValue('B' . $fila, "REPORTE DE VENTAS CONSOLIDADO POR EVENTOS FACTURADOS");
    }

    public function formDataClientes(array $data, Worksheet $sheet, int $fila): void
    {
        $currentRow = $fila;

        $sheet->setCellValue('B' . $currentRow, "Fecha");
        $sheet->setCellValue('C' . $currentRow, "Evento");
        $sheet->setCellValue('D' . $currentRow, "Servicio");
        $sheet->setCellValue('E' . $currentRow, "Nombre Cliente");
        $sheet->setCellValue('F' . $currentRow, "Código de Empleado");
        $sheet->setCellValue('G' . $currentRow, "Cantidad");
        $currentRow++;

        $granTotal = 0;
        foreach ($data as $item) {
            $fecha = $item['fecha_emision'];
            $planes = $item['data'];
            $numPlanes = count($planes);

            $sheet->mergeCells("B$currentRow:B" . ($currentRow + $numPlanes - 1));
            $sheet->setCellValue("B$currentRow", $fecha);

            foreach ($planes as $index => $plan) {
                $sheet->setCellValue('C' . ($currentRow + $index), mb_strtoupper($plan['evento'], 'UTF-8'));
                $sheet->setCellValue('D' . ($currentRow + $index), mb_strtoupper($plan['nombre_servicio'], 'UTF-8'));
                $sheet->setCellValue('E' . ($currentRow + $index), mb_strtoupper($plan['cliente'], 'UTF-8'));
                $sheet->setCellValue('F' . ($currentRow + $index), $plan['clie_docnum']);
                $sheet->setCellValue('G' . ($currentRow + $index), $plan['cantidad_total_facturada']);
                $granTotal += $plan['cantidad_total_facturada'];
            }
            $currentRow += $numPlanes;
        }

        $initialRow = $fila - 1;
        $lastRow = $currentRow;
        $sheet->getStyle("B$initialRow:G$lastRow")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Estilo de los encabezados
        $sheet->getStyle("B$initialRow:G$initialRow")->getFont()->setBold(true);
        $sheet->getStyle("B$fila:G$fila")->getFont()->setBold(true);

        // Ajustar ancho automático de las columnas
        foreach (range('B', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Colocar el título de reporte
        $sheet->mergeCells("B$initialRow:G$initialRow");
        $sheet->setCellValue('B' . $initialRow, "REPORTE DE VENTAS DETALLADO POR CLIENTE POR EVENTOS FACTURADOS");

        // Colocar total de cierre
        $sheet->mergeCells("B$currentRow:F$currentRow");
        $sheet->setCellValue('B' . $currentRow, 'TOTAL CIERRE');
        $sheet->setCellValue('G' . $currentRow, $granTotal);
        $sheet->getStyle("B$currentRow:G$currentRow")->getFont()->setBold(true);
        $sheet->getStyle("B$currentRow:G$currentRow")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }



    public function saveFile(Spreadsheet $spreadsheet, string $reportName): array
    {
        // Guardar el archivo
        $writer = new Xlsx($spreadsheet);
        $writer->save("$reportName.xlsx");

        // Guardar el archivo en el servidor
        $filename = "$reportName.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);
        return [
            "writer" => $writer,
            "filename" => $filename
        ];
    }
}
