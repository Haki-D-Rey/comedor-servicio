<?php

namespace App\DTO\Catalogo;

use JsonSerializable;

class CargosDTO implements JsonSerializable
{
    private $id;
    private $nombre;
    private $descripcion;
    private $codigo_interno;
    private $fecha_creacion;
    private $fecha_modificacion = null;
    private $estado;

    public function __construct(
        ?int $id,
        string $nombre,
        string $descripcion,
        string $codigo_interno,
        \DateTime $fecha_creacion,
        ?\DateTime $fecha_modificacion,
        bool $estado
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->codigo_interno = $codigo_interno;
        $this->fecha_creacion = $fecha_creacion;
        $this->fecha_modificacion = $fecha_modificacion;
        $this->estado = $estado;
    }

    // Getters
    public function getId(): int
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

    public function getCodigoInterno(): string
    {
        return $this->codigo_interno;
    }

    public function getFechaCreacion(): \DateTime
    {
        return $this->fecha_creacion;
    }

    public function getFechaModificacion(): ?\DateTime
    {
        return $this->fecha_modificacion;
    }

    public function getEstado(): bool
    {
        return $this->estado;
    }

    // Implementación de JsonSerializable
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'codigo_interno' => $this->codigo_interno,
            'fecha_creacion' => $this->fecha_creacion->format(\DateTime::ISO8601),
            'fecha_modificacion' => $this->fecha_modificacion ? $this->fecha_modificacion->format(\DateTime::ISO8601) : null,
            'estado' => $this->estado
        ];
    }
}
