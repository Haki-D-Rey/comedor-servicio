<?php

namespace App\Services\Publico;

use App\DTO\Publico\VentasDTO;
use App\Repository\Publico\Interface\VentasRepositoryInterface;

class VentasServices
{
    private VentasRepositoryInterface $ventasRepositoryInterface;

    public function __construct(VentasRepositoryInterface $ventasRepositoryInterface)
    {
        $this->ventasRepositoryInterface = $ventasRepositoryInterface;
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
        return $this->ventasRepositoryInterface->getReportVentas($filtros);
    }
}
