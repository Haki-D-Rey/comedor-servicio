<?php

namespace App\Repository\Publico\Interface;

use App\DTO\Publico\VentasDTO;
use App\DTO\Publico\VentasFacturacionDTO;

interface VentasRepositoryInterface
{
    /**
     * @return VentasDTO[]
     */
    public function getAllVentas(): array;

    /**
     * @param int $id
     * @return VentasDTO|null
     */
    public function getVentaById(int $id): ?VentasDTO;

    /**
     * @param VentasFacturacionDTO[] $ventaDTO
     * @return array
     */
    public function createVenta(array $ventaDTO): array;

    /**
     * @param int $id
     * @param VentasDTO $ventaDTO
     * @return void
     */
    public function updateVenta(int $id, VentasDTO $ventaDTO): void;

    /**
     * @param int $id
     * @return void
     */
    public function deleteVenta(int $id): void;

    public function getReportVentas(array $filtros): array;

    public function getDetailedReportByClient(array $filtros): array;

    public function getConsolidatedReportByZone(array $filtros): array;
}
