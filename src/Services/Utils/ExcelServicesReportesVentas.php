<?php

namespace App\Services\Utils;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExcelServicesReportesVentas
{
    public function setDocumentProperties(Spreadsheet $spreadsheet)
    {
        // Configurar propiedades del documento
        return $spreadsheet->getProperties()->setCreator("Tu nombre")
            ->setLastModifiedBy("Tu nombre")
            ->setTitle("Reporte de Inscripciones XXI Congreso y Precongreso Cientifíco Médico")
            ->setSubject("Reporte de inscripciones")
            ->setDescription("Este archivo contiene el reporte de inscripciones.")
            ->setKeywords("inscripciones reporte excel")
            ->setCategory("Reporte");
    }

    public function setHeaders(Worksheet $sheet)
    {
        return $sheet;
    }

    public function formData(array $data, Worksheet $sheet, int $fila): void
    {
        $currentRow = $fila; // Empezar desde la fila inicial proporcionada
        $totalServicios = [];
        $granTotal = 0;

        // 1. Recolectar y ordenar servicios únicos según el campo "order"
        $serviciosOrdenados = [];
        foreach ($data as $item) {
            foreach ($item['data'] as $servicio) {
                $serviciosOrdenados[$servicio['nombre_servicio']] = $servicio['order'];
            }
        }
        // Ordenar los servicios por "order"
        asort($serviciosOrdenados);

        // 2. Asignar columnas dinámicas según el orden
        $columnMap = [];
        $colIndex = ord('C'); // Empezar desde la columna C
        foreach ($serviciosOrdenados as $servicio => $order) {
            $columnMap[$servicio] = chr($colIndex); // Asignar columna dinámica
            $colIndex++;
        }

        // 3. ENCABEZADO PRINCIPAL: Evento
        $sheet->mergeCells("B$currentRow:" . chr($colIndex - 1) . "$currentRow");
        $sheet->setCellValue("B$currentRow", "Zona asignada para el Auditoria");
        $sheet->getStyle("B$currentRow:" . chr($colIndex - 1) . "$currentRow")->getFont()->setBold(true);
        $sheet->getStyle("B$currentRow:" . chr($colIndex - 1) . "$currentRow")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $currentRow++;

        // 4. Encabezados dinámicos en base al orden
        $sheet->setCellValue('B' . $currentRow, 'Fecha');
        foreach ($columnMap as $servicio => $col) {
            $sheet->setCellValue($col . $currentRow, $servicio); // Nombre del servicio en el orden correcto
        }
        $sheet->getStyle("B$currentRow:" . chr($colIndex - 1) . "$currentRow")->getFont()->setBold(true);
        $currentRow++;

        // 5. Datos dinámicos
        foreach ($data as $item) {
            $fecha = $item['fecha_emision'];
            $servicioTotales = array_fill_keys(array_values($columnMap), 0); // Inicializar totales por servicio

            foreach ($item['data'] as $plan) {
                if (isset($columnMap[$plan['nombre_servicio']])) {
                    $col = $columnMap[$plan['nombre_servicio']];
                    $servicioTotales[$col] += $plan['cantidad_total_facturada'];
                }
            }

            // Escribir fila de datos
            $sheet->setCellValue('B' . $currentRow, $fecha);
            foreach ($servicioTotales as $col => $cantidad) {
                $sheet->setCellValue($col . $currentRow, $cantidad > 0 ? $cantidad : '');
            }

            // Acumular totales generales
            foreach ($servicioTotales as $col => $cantidad) {
                $totalServicios[$col] = ($totalServicios[$col] ?? 0) + $cantidad;
                $granTotal += $cantidad;
            }
            $currentRow++;
        }

        // 6. Totales por servicio
        $sheet->setCellValue('B' . $currentRow, 'TOTAL SERVICIOS');
        foreach ($totalServicios as $col => $total) {
            $sheet->setCellValue($col . $currentRow, $total);
        }
        $sheet->getStyle("B$currentRow:" . chr($colIndex - 1) . "$currentRow")->getFont()->setBold(true);
        $currentRow++;

        // 7. Total cierre
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
    }


    // public function formData(array $data, Worksheet $sheet, int $fila): void
    // {

    //     $data = [
    //         [
    //             "id_zona"=> 4,
    //             "fecha_emision" => "2024-12-12",
    //             "data" => [
    //                 [
    //                     "evento" => "Zona asignada para el Auditoria del Eficio 1A - HMEADB",
    //                     "nombre_servicio" => "Cena Navideña",
    //                     "cantidad_total_facturada" => 15
    //                 ],
    //                 [
    //                     "evento" => "Zona asignada para el Auditoria del Eficio 1A - HMEADB",
    //                     "nombre_servicio" => "Desayuno Ejecutivo",
    //                     "cantidad_total_facturada" => 20
    //                 ],
    //                 [
    //                     "evento" => "Zona asignada para el Auditoria del Eficio 1A - HMEADB",
    //                     "nombre_servicio" => "Almuerzo Corporativo",
    //                     "cantidad_total_facturada" => 25
    //                 ]
    //             ]
    //         ],
    //         [
    //             "id_zona"=> 4,
    //             "fecha_emision" => "2024-12-13",
    //             "data" => [
    //                 [
    //                     "evento" => "Zona asignada para el Auditoria del Eficio 1A - HMEADB",
    //                     "nombre_servicio" => "Cena Navideña",
    //                     "cantidad_total_facturada" => 15
    //                 ],
    //                 [
    //                     "evento" => "Zona asignada para el Auditoria del Eficio 1A - HMEADB",
    //                     "nombre_servicio" => "Desayuno Ejecutivo",
    //                     "cantidad_total_facturada" => 20
    //                 ],
    //                 [
    //                     "evento" => "Zona asignada para el Auditoria del Eficio 1A - HMEADB",
    //                     "nombre_servicio" => "Almuerzo Corporativo",
    //                     "cantidad_total_facturada" => 25
    //                 ]
    //             ]
    //         ],
    //         [
    //             "id_zona"=> 7,
    //             "fecha_emision" => "2024-12-15",
    //             "data" => [
    //                 [
    //                     "evento" => "Auditoria del Edificio 2B - HMEADB",
    //                     "nombre_servicio" => "Cena de Cierre",
    //                     "cantidad_total_facturada" => 30
    //                 ]
    //             ]
    //         ]
    //     ];



    //     $currentRow = $fila;  // Aseguramos que la fila inicial es 4
    //     // Iterar los datos
    //     foreach ($data as $item) {
    //         $fecha = $item['fecha_emision'];
    //         $planes = $item['data'];
    //         $numPlanes = count($planes);

    //         // Fusionar y colocar la fecha
    //         $sheet->mergeCells("B$currentRow:B" . ($currentRow + $numPlanes - 1));
    //         $sheet->setCellValue("B$currentRow", $fecha);

    //         foreach ($planes as $index => $plan) {
    //             $sheet->setCellValue('C' . ($currentRow + $index), $plan['evento']);
    //             $sheet->setCellValue('D' . ($currentRow + $index), $plan['nombre_servicio']);
    //             $sheet->setCellValue('E' . ($currentRow + $index), $plan['cantidad_total_facturada']);
    //         }

    //         $currentRow += $numPlanes;
    //     }

    //     // Aplicar estilos a todo el rango de datos
    //     $initalRow = $fila - 2;
    //     $lastRow = $currentRow - 1;
    //     $sheet->getStyle("B$initalRow:E$lastRow")->applyFromArray([
    //         'borders' => [
    //             'allBorders' => [
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
    //                 'color' => ['argb' => 'FF000000'],
    //             ],
    //         ],
    //         'alignment' => [
    //             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //             'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //         ],
    //     ]);
    //     $sheet->getStyle('B2:E2')->getFont()->setBold(true);

    //     // Ajustar ancho automático de las columnas
    //     foreach (range('B', 'E') as $col) {
    //         $sheet->getColumnDimension($col)->setAutoSize(true);
    //     }
    // }


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