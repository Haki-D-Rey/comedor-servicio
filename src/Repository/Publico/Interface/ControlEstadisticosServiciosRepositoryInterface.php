<?php

namespace App\Repository\Publico\Interface;

use App\DTO\Publico\ControlEstadisticosServiciosDTO;

interface ControlEstadisticosServiciosRepositoryInterface
{
    /**
     * @return ControlEstadisticosServiciosDTO[]
     */
    public function getAllControlEstadisticosServicios(): array;

    /**
     * @param int $id
     * @return ControlEstadisticosServiciosDTO|null
     */
    public function getControlEstadisticosServiciosById(int $id): ?ControlEstadisticosServiciosDTO;


    /**
     * @param string $date
     * @return ControlEstadisticosServiciosDTO|null
     */
    public function getControlEstadisticosServiciosByIdForDate(string $date): ?array;

    /**
     * @param array $controlEstadisticosServicios
     * @return void
     */
    public function createFormControlEstadisticosServicios(array $controlEstadisticosServicios): void;

    /**
     * @param ControlEstadisticosServiciosDTO $controlEstadisticosServiciosDTO
     * @return void
     */
    public function createControlEstadisticosServicios(ControlEstadisticosServiciosDTO $controlEstadisticosServiciosDTO): void;

    /**
     * @param int $id
     * @param ControlEstadisticosServiciosDTO $controlEstadisticosServiciosDTO
     * @return void
     */
    public function updateControlEstadisticosServicios(int $id, ControlEstadisticosServiciosDTO $controlEstadisticosServiciosDTO): void;

    /**
     * @param int $id
     * @return void
     */
    public function deleteControlEstadisticosServicios(int $id): void;
}
