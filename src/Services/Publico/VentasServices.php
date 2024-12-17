<?php

namespace App\Services\Publico;

use App\DTO\Publico\VentasDTO;
use App\Repository\Publico\Interface\VentasRepositoryInterface;
use App\Services\Utils\ExcelServicesReportesVentas;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class VentasServices
{
    private VentasRepositoryInterface $ventasRepositoryInterface;
    private ExcelServicesReportesVentas $excelServicesReportesVentas;

    public function __construct(VentasRepositoryInterface $ventasRepositoryInterface, ExcelServicesReportesVentas $excelServicesReportesVentas)
    {
        $this->ventasRepositoryInterface = $ventasRepositoryInterface;
        $this->excelServicesReportesVentas = $excelServicesReportesVentas;
    }

    public function getAllVentas(): array
    {
        return $this->ventasRepositoryInterface->getAllVentas();
    }

    public function getVentaById(int $id): ?VentasDTO
    {
        return $this->ventasRepositoryInterface->getVentaById($id);
    }

    public function createVenta(array $clienteCreditoPeriodicoDTO): array
    {
        return $this->ventasRepositoryInterface->createVenta($clienteCreditoPeriodicoDTO);
    }

    public function updateVenta(int $id, VentasDTO $clienteCreditoPeriodicoDTO): void
    {
        $this->ventasRepositoryInterface->updateVenta($id, $clienteCreditoPeriodicoDTO);
    }

    public function deleteVenta(int $id): void
    {
        $this->ventasRepositoryInterface->deleteVenta($id);
    }

    public function getReportVentas(array $filtros): array
    {

        $resultado = $this->ventasRepositoryInterface->getReportVentas($filtros);
        $month = date('F');
        $year = date('Y');
        // Traducir el nombre del mes al español
        $months = [
            'January' => 'Enero',
            'February' => 'Febrero',
            'March' => 'Marzo',
            'April' => 'Abril',
            'May' => 'Mayo',
            'June' => 'Junio',
            'July' => 'Julio',
            'August' => 'Agosto',
            'September' => 'Septiembre',
            'October' => 'Octubre',
            'November' => 'Noviembre',
            'December' => 'Diciembre'
        ];

        $current_month = $months[$month];
        // Crear una instancia de PhpSpreadsheet
        $spreadsheet = new Spreadsheet();

        $this->excelServicesReportesVentas->setDocumentProperties($spreadsheet);
        // Crear una nueva hoja de cálculo
        $sheetSI = $spreadsheet->getActiveSheet();
        $sheetSI->setTitle("REPORTE - $current_month $year");
        $this->excelServicesReportesVentas->setHeaders($sheetSI);
        $this->excelServicesReportesVentas->formData($resultado, $sheetSI, 2);
        // Guardar el archivo excel en el servidor
        $arrayFile = $this->excelServicesReportesVentas->saveFile($spreadsheet, "Reporte Servicios Alimentacios HMIL $year");
 
        // $mailer = new EmailController();
        // // Configurar el correo electrónico
        // $this->mailServer = $mailer->sendEmail($parametrosCorreo);
        // // Adjuntar el archivo Excel
        // $this->mailServer->addAttachment($filename, basename($filename));
        // // Enviar el correo electrónico
        // $this->mailServer->send();
        // // Configurar la respuesta para descargar el archivo
        return $arrayFile;
    }
}
