<?php

namespace App\Services\Publico;

use App\DTO\Publico\ClientesDTO;
use App\Repository\Publico\Interface\ClientesRepositoryInterface;

class ClientesServices
{
    private $clientesRepository;

    public function __construct(ClientesRepositoryInterface $clientesRepository)
    {
        $this->clientesRepository = $clientesRepository;
    }

    public function getAllClientes(): array
    {
        try {
            return $this->clientesRepository->getAllClientes();
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener los clientes: ' . $e->getMessage());
        }
    }

    public function getClienteById(int $id): ?ClientesDTO
    {
        try {
            return $this->clientesRepository->getClienteById($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el cliente: ' . $e->getMessage());
        }
    }

    public function createCliente(array $clientesDTO): void
    {
        try {
            $this->clientesRepository->createCliente($clientesDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al crear el cliente: ' . $e->getMessage());
        }
    }

    public function updateCliente(int $id, ClientesDTO $clientesDTO): void
    {
        try {
            $this->clientesRepository->updateCliente($id, $clientesDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al actualizar el cliente: ' . $e->getMessage());
        }
    }

    public function deleteCliente(int $id): void
    {
        try {
            $this->clientesRepository->deleteCliente($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al eliminar el cliente: ' . $e->getMessage());
        }
    }

    public function getSearchClients(array $filtro): array
    {
        try {
            return $this->clientesRepository->getSearchClients($filtro);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al eliminar el cliente: ' . $e->getMessage());
        }
    }

    public function getClientsRelationalIdentification(array $filtro): array
    {
        try {
            return $this->clientesRepository->getClientsRelationalIdentification($filtro);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al eliminar el cliente: ' . $e->getMessage());
        }
    }

    public function getValidateFormById(array $filtro): array
    {
        try {
            return $this->clientesRepository->getValidateFormById($filtro);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al eliminar el cliente: ' . $e->getMessage());
        }
    }
}
