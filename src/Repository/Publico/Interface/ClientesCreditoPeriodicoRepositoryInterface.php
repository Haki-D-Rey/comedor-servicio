<?php

namespace App\Repository\Publico\Interface;

use App\DTO\Publico\ClientesCreditoPeriodicoDTO;

interface ClientesCreditoPeriodicoRepositoryInterface
{
    /**
     * Obtiene todos los registros de ClienteCreditoPeriodico.
     * 
     * @return ClientesCreditoPeriodicoDTO[]
     */
    public function getAllClienteCreditoPeriodico(): array;

    /**
     * Obtiene un ClienteCreditoPeriodico por su ID.
     * 
     * @param int $id
     * @return ClienteCreditoPeriodicoDTO|null
     */
    public function getClienteCreditoPeriodicoById(int $id): ?ClientesCreditoPeriodicoDTO;

    /**
     * Crea un nuevo registro de ClienteCreditoPeriodico.
     * 
     * @param ClientesCreditoPeriodicoDTO[] $clienteCreditoPeriodicoDTO
     * @return void
     */
    public function createClienteCreditoPeriodico(array $clienteCreditoPeriodicoDTO): void;

    /**
     * Actualiza un registro de ClienteCreditoPeriodico.
     * 
     * @param int $id
     * @param ClientesCreditoPeriodicoDTO $clienteCreditoPeriodicoDTO
     * @return void
     */
    public function updateClienteCreditoPeriodico(int $id, ClientesCreditoPeriodicoDTO $clienteCreditoPeriodicoDTO): void;

    /**
     * Elimina un registro de ClienteCreditoPeriodico.
     * 
     * @param int $id
     * @return void
     */
    public function deleteClienteCreditoPeriodico(int $id): void;
}
