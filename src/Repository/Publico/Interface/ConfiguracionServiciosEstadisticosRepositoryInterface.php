<?php

namespace App\Repository\Publico\Interface;

use App\DTO\Publico\ConfiguracionServiciosEstadisticosDTO;

interface ConfiguracionServiciosEstadisticosRepositoryInterface
{
    /**
     * @return ConfiguracionServiciosEstadisticosDTO[]
     */
    public function getAllConfiguracionServiciosEstadisticos(): array;

    /**
     * @param int $id
     * @return ConfiguracionServiciosEstadisticosDTO|null
     */
    public function getConfiguracionServiciosEstadisticosById(int $id): ?ConfiguracionServiciosEstadisticosDTO;


    // /**
    //  * @param string $date
    //  * @return array|null
    //  */
    // public function getConfiguracionServiciosEstadisticosByIdForDate(string $date): ?array;

    /**
     * @param ConfiguracionServiciosEstadisticosDTO $controlEstadisticosServiciosDTO
     * @return ConfiguracionServiciosEstadisticosDTO
     */
    public function createConfiguracionServiciosEstadisticos(ConfiguracionServiciosEstadisticosDTO $controlEstadisticosServiciosDTO): ConfiguracionServiciosEstadisticosDTO;

    /**
     * @param int $id
     * @param ConfiguracionServiciosEstadisticosDTO $controlEstadisticosServiciosDTO
     * @return void
     */
    public function updateConfiguracionServiciosEstadisticos(int $id, ConfiguracionServiciosEstadisticosDTO $controlEstadisticosServiciosDTO): void;

    /**
     * @param int $id
     * @return void
     */
    public function deleteConfiguracionServiciosEstadisticos(int $id): void;
}
