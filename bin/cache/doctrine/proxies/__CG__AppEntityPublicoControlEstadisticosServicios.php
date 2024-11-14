<?php

namespace Proxies\__CG__\App\Entity\Publico;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class ControlEstadisticosServicios extends \App\Entity\Publico\ControlEstadisticosServicios implements \Doctrine\ORM\Proxy\InternalProxy
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
        "\0".parent::class."\0".'cantidadAnulada' => [parent::class, 'cantidadAnulada', null],
        "\0".parent::class."\0".'cantidadFirmada' => [parent::class, 'cantidadFirmada', null],
        "\0".parent::class."\0".'configuracionServiciosEstadisticos' => [parent::class, 'configuracionServiciosEstadisticos', null],
        "\0".parent::class."\0".'detalleZonaServicioHorario' => [parent::class, 'detalleZonaServicioHorario', null],
        "\0".parent::class."\0".'estado' => [parent::class, 'estado', null],
        "\0".parent::class."\0".'fechaCorte' => [parent::class, 'fechaCorte', null],
        "\0".parent::class."\0".'fechaCreacion' => [parent::class, 'fechaCreacion', null],
        "\0".parent::class."\0".'fechaModificacion' => [parent::class, 'fechaModificacion', null],
        "\0".parent::class."\0".'id' => [parent::class, 'id', null],
        "\0".parent::class."\0".'uuid' => [parent::class, 'uuid', null],
        'cantidadAnulada' => [parent::class, 'cantidadAnulada', null],
        'cantidadFirmada' => [parent::class, 'cantidadFirmada', null],
        'configuracionServiciosEstadisticos' => [parent::class, 'configuracionServiciosEstadisticos', null],
        'detalleZonaServicioHorario' => [parent::class, 'detalleZonaServicioHorario', null],
        'estado' => [parent::class, 'estado', null],
        'fechaCorte' => [parent::class, 'fechaCorte', null],
        'fechaCreacion' => [parent::class, 'fechaCreacion', null],
        'fechaModificacion' => [parent::class, 'fechaModificacion', null],
        'id' => [parent::class, 'id', null],
        'uuid' => [parent::class, 'uuid', null],
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
