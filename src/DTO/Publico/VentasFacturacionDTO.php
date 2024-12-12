<?php

namespace App\DTO\Publico;

class VentasFacturacionDTO
{
    public string $cod_identificacion;
    public int $idDetalleZonaServicioHorario;
    public int $cantidadFacturada;
    public string $codigo_internoIF;

    public function __construct(
        string $cod_identificacion,
        int $idDetalleZonaServicioHorario,
        int $cantidadFacturada,
        string $codigo_internoIF
    ) {
        $this->cod_identificacion = $cod_identificacion;
        $this->idDetalleZonaServicioHorario = $idDetalleZonaServicioHorario;
        $this->cantidadFacturada = $cantidadFacturada;
        $this->codigo_internoIF = $codigo_internoIF;
    }


    public function getCodIdentificacion(): ?string
    {
        return $this->cod_identificacion;
    }

    public function getIdDetalleZonaServicioHorario(): int
    {
        return $this->idDetalleZonaServicioHorario;
    }

    public function getCantidadFacturada(): int
    {
        return $this->cantidadFacturada;
    }

    public function getCodigoInternoIF(): string
    {
        return $this->codigo_internoIF;
    }
}
