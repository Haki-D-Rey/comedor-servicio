<?php

namespace App\Repository\Publico\Interface;

use App\DTO\Publico\ClientesDTO;

interface ClientesRepositoryInterface
{
    /**
     * @return ClienteDTO[]
     */
    public function getAllClientes(): array;

    /**
     * @param int $id
     * @return ClientesDTO|null
     */
    public function getClienteById(int $id): ?ClientesDTO;

    /**
     * @param ClientesDTO[] $clienteDTO
     * @return void
     */
    public function createCliente(array $clienteDTO): void;

    /**
     * @param int $id
     * @param ClientesDTO $clienteDTO
     * @return void
     */
    public function updateCliente(int $id, ClientesDTO $clienteDTO): void;

    /**
     * @param int $id
     * @return void
     */
    public function deleteCliente(int $id): void;

    /**
     * @param array $filtro
     * @return array
     */
    public function getSearchClients(array $filtro): array;

    /**
     * @param array $filtro
     * @return array
     */
    public function getClientsRelationalIdentification(array $filtro): array;

    /**
     * @param array $filtro
     * @return array
     */
    public function getValidateFormById(array $filtro): array;
}
