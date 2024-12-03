<?php

namespace App\Repository\Catalogo\Interface;

use App\DTO\DetalleZonasServicioHorarioDTO;

interface DetalleZonaServicioHorarioRepositoryInterface
{
    /**
     * @return DetalleZonasServicioHorarioDTO[]
     */
    public function getAllDetalleZonasServicioHorario(): array;

    /**
     * @param int $id
     * @return array|null
     */
    public function getAllDetalleZonaServicioHorarioByZonaUsuario($id): array;

    /**
     * @param int $id
     * @return array|null
     */
    public function getAllDetalleZonaServicioHorarioByIdZonaUsuario($id): array;

    /**
     * @param int $id
     * @return DetalleZonasServicioHorarioDTO|null
     */
    public function getDetalleZonasServicioHorarioById(int $id): ?DetalleZonasServicioHorarioDTO;

    /**
     * @param array $DetalleZonasServicioHorarioDTO
     * @return bool
     */
    public function createDetalleZonasServicioHorario(array $DetalleZonaServicioHorarioDTO): void;

    /**
     * @param int $id
     * @param DetalleZonasServicioHorarioDTO $DetalleZonasServicioHorarioDTO
     * @return bool
     */
    public function updateDetalleZonasServicioHorario(int $id, DetalleZonasServicioHorarioDTO $DetalleZonaServicioHorarioDTO): void;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteDetalleZonasServicioHorario(int $id): void;
}
