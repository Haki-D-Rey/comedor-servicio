<?php

namespace App\DTO;

use JsonSerializable;

class ListaCatalogoDetalleDTO implements JsonSerializable
{
    private ?int $id;
    private int $id_lista_catalogo;
    private int $id_valor;
    private string $codigo_interno;
    private \DateTime $fecha_creacion;
    private ?\DateTime $fecha_modificacion;
    private bool $estado;

    public function __construct(
        ?int $id,
        int $id_lista_catalogo,
        int $id_valor,
        string $codigo_interno,
        \DateTime $fecha_creacion,
        ?\DateTime $fecha_modificacion,
        bool $estado
    ) {
        $this->id = $id;
        $this->id_lista_catalogo = $id_lista_catalogo;
        $this->id_valor = $id_valor;
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

    public function getIdListaCatalogo(): int
    {
        return $this->id_lista_catalogo;
    }

    public function getIdValor(): int
    {
        return $this->id_valor;
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

    // Implementación de jsonSerialize para la serialización JSON
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'id_lista_catalogo' => $this->id_lista_catalogo,
            'id_valor' => $this->id_valor,
            'codigo_interno' => $this->codigo_interno,
            'fecha_creacion' => $this->fecha_creacion->format(\DateTime::ISO8601),
            'fecha_modificacion' => $this->fecha_modificacion ? $this->fecha_modificacion->format(\DateTime::ISO8601) : null,
            'estado' => $this->estado,
        ];
    }
}
