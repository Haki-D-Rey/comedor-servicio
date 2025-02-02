<?php

namespace Proxies\__CG__\App\Entity\Publico;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class ClientesCreditoPeriodico extends \App\Entity\Publico\ClientesCreditoPeriodico implements \Doctrine\ORM\Proxy\InternalProxy
{
    use \Symfony\Component\VarExporter\LazyGhostTrait {
        initializeLazyObject as private;
        setLazyObjectAsInitialized as public __setInitialized;
        isLazyObjectInitialized as private;
        createLazyGhost as private;
        resetLazyObject as private;
    }

    public function __load(): void
    {
        $this->initializeLazyObject();
    }
    

    private const LAZY_OBJECT_PROPERTY_SCOPES = [
        "\0".parent::class."\0".'cantidadCreditoDisponible' => [parent::class, 'cantidadCreditoDisponible', null],
        "\0".parent::class."\0".'cantidadCreditoLimite' => [parent::class, 'cantidadCreditoLimite', null],
        "\0".parent::class."\0".'cantidadCreditoUsado' => [parent::class, 'cantidadCreditoUsado', null],
        "\0".parent::class."\0".'detalleZonaServicioHorarioClienteFacturacion' => [parent::class, 'detalleZonaServicioHorarioClienteFacturacion', null],
        "\0".parent::class."\0".'estado' => [parent::class, 'estado', null],
        "\0".parent::class."\0".'fechaCreacion' => [parent::class, 'fechaCreacion', null],
        "\0".parent::class."\0".'fechaModificacion' => [parent::class, 'fechaModificacion', null],
        "\0".parent::class."\0".'id' => [parent::class, 'id', null],
        "\0".parent::class."\0".'idDetalleZonaServicioHorarioClienteFacturacion' => [parent::class, 'idDetalleZonaServicioHorarioClienteFacturacion', null],
        "\0".parent::class."\0".'periodoFinal' => [parent::class, 'periodoFinal', null],
        "\0".parent::class."\0".'periodoInicial' => [parent::class, 'periodoInicial', null],
        'cantidadCreditoDisponible' => [parent::class, 'cantidadCreditoDisponible', null],
        'cantidadCreditoLimite' => [parent::class, 'cantidadCreditoLimite', null],
        'cantidadCreditoUsado' => [parent::class, 'cantidadCreditoUsado', null],
        'detalleZonaServicioHorarioClienteFacturacion' => [parent::class, 'detalleZonaServicioHorarioClienteFacturacion', null],
        'estado' => [parent::class, 'estado', null],
        'fechaCreacion' => [parent::class, 'fechaCreacion', null],
        'fechaModificacion' => [parent::class, 'fechaModificacion', null],
        'id' => [parent::class, 'id', null],
        'idDetalleZonaServicioHorarioClienteFacturacion' => [parent::class, 'idDetalleZonaServicioHorarioClienteFacturacion', null],
        'periodoFinal' => [parent::class, 'periodoFinal', null],
        'periodoInicial' => [parent::class, 'periodoInicial', null],
    ];

    public function __isInitialized(): bool
    {
        return isset($this->lazyObjectState) && $this->isLazyObjectInitialized();
    }

    public function __serialize(): array
    {
        $properties = (array) $this;
        unset($properties["\0" . self::class . "\0lazyObjectState"]);

        return $properties;
    }
}
