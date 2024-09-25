<?php

namespace App\DTO;

use Doctrine\DBAL\Types\TimeType;
use JsonSerializable;

class DetalleZonasServicioHorarioDTO implements JsonSerializable
{
    private ?int $id;
    private int $idServiciosProductosDetalles;
    private int $idHorario;
    private int $idZona;
    private string $nombre;
    private string $descripcion;
    private string $codigo_interno;
    private \DateTime $fecha_creacion;
    private ?\DateTime $fecha_modificacion;
    private bool $estado;

    public function __construct(
        ?int $id,
        int $idServiciosProductosDetalles,
        int $idHorario,
        int $idZona,
        string $nombre,
        string $descripcion,
        string $codigo_interno,
        \DateTime $fecha_creacion,
        ?\DateTime $fecha_modificacion,
        bool $estado
    ) {
        $this->id = $id;
        $this->idServiciosProductosDetalles = $idServiciosProductosDetalles;
        $this->idHorario = $idHorario;
        $this->idZona = $idZona;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->codigo_interno = $codigo_interno;
        $this->fecha_creacion = $fecha_creacion;
        $this->fecha_modificacion = $fecha_modificacion;
        $this->estado = $estado;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function getcodigo_interno(): string
    {
        return $this->codigo_interno;
    }

    public function getfecha_creacion(): \DateTime
    {
        return $this->fecha_creacion;
    }

    public function getfecha_modificacion(): ?\DateTime
    {
        return $this->fecha_modificacion;
    }

    public function getEstado(): bool
    {
        return $this->estado;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'idServiciosProductosDetalles' => $this->idServiciosProductosDetalles,
            'idHorario' => $this-> idHorario,
            'idZona' => $this-> idZona,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'codigo_interno' => $this->codigo_interno,
            'fecha_creacion' => $this->fecha_creacion->format(\DateTime::ISO8601),
            'fecha_modificacion' => $this->fecha_modificacion ? $this->fecha_modificacion->format(\DateTime::ISO8601) : null,
            'estado' => $this->estado,
        ];
    }
}