<?php

namespace App\DTO;

use JsonSerializable;

class ServiciosProductosDetallesDTO implements JsonSerializable
{
    private ?int $id;
    private int $idSistemas;
    private int $idTipoServicios;
    private int $idServicioProductos;
    private string $nombre;
    private string $descripcion;
    private string $codigo_interno;
    private \DateTime $fecha_creacion;
    private ?\DateTime $fecha_modificacion;
    private bool $estado;
    private \DateTime $periodo_inicial;
    private \DateTime $periodo_final;
    private int $orden = 1;

    public function __construct(
        ?int $id,
        int $idSistemas,
        int $idTipoServicios,
        int $idServicioProductos,
        string $nombre,
        string $descripcion,
        string $codigo_interno,
        \DateTime $fecha_creacion,
        ?\DateTime $fecha_modificacion,
        bool $estado,
        \DateTime $periodo_inicial,
        \DateTime $periodo_final,
        int $orden
        
    ) {
        $this->id = $id;
        $this->idSistemas = $idSistemas;
        $this->idTipoServicios = $idTipoServicios;
        $this->idServicioProductos = $idServicioProductos;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->codigo_interno = $codigo_interno;
        $this->fecha_creacion = $fecha_creacion;
        $this->fecha_modificacion = $fecha_modificacion;
        $this->estado = $estado;
        $this->periodo_inicial = $periodo_inicial;
        $this->periodo_final = $periodo_final;
        $this->orden = $orden;
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

    public function getPeriodoInicial(): \DateTime
    {
        return $this->periodo_inicial;
    }

    public function getPeriodoFinal(): \DateTime
    {
        return $this->periodo_final;
    }

    public function getOrden(): int
    {
        return $this->orden;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'idSistemas' => $this->idSistemas,
            'idTipoServicios' => $this-> idTipoServicios,
            'idServicioProductos' => $this-> idServicioProductos,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'codigo_interno' => $this->codigo_interno,
            'fecha_creacion' => $this->fecha_creacion->format(\DateTime::ISO8601),
            'fecha_modificacion' => $this->fecha_modificacion ? $this->fecha_modificacion->format(\DateTime::ISO8601) : null,
            'estado' => $this->estado,
            'periodo_incial' => $this->periodo_inicial,
            'periodo_final' => $this->periodo_final,
            'orden' => $this->orden
        ];
    }
}
