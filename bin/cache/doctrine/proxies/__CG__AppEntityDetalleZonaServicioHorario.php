<?php

namespace Proxies\__CG__\App\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class DetalleZonaServicioHorario extends \App\Entity\DetalleZonaServicioHorario implements \Doctrine\ORM\Proxy\InternalProxy
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
        "\0".parent::class."\0".'codigo_interno' => [parent::class, 'codigo_interno', null],
        "\0".parent::class."\0".'controlEstadisticosServicios' => [parent::class, 'controlEstadisticosServicios', null],
        "\0".parent::class."\0".'descripcion' => [parent::class, 'descripcion', null],
        "\0".parent::class."\0".'estado' => [parent::class, 'estado', null],
        "\0".parent::class."\0".'fecha_creacion' => [parent::class, 'fecha_creacion', null],
        "\0".parent::class."\0".'fecha_modificacion' => [parent::class, 'fecha_modificacion', null],
        "\0".parent::class."\0".'horario' => [parent::class, 'horario', null],
        "\0".parent::class."\0".'id' => [parent::class, 'id', null],
        "\0".parent::class."\0".'idHorario' => [parent::class, 'idHorario', null],
        "\0".parent::class."\0".'idServiciosProductosDetalles' => [parent::class, 'idServiciosProductosDetalles', null],
        "\0".parent::class."\0".'idZonaUsuario' => [parent::class, 'idZonaUsuario', null],
        "\0".parent::class."\0".'nombre' => [parent::class, 'nombre', null],
        "\0".parent::class."\0".'serviciosProductosDetalles' => [parent::class, 'serviciosProductosDetalles', null],
        "\0".parent::class."\0".'zonaUsuario' => [parent::class, 'zonaUsuario', null],
        'codigo_interno' => [parent::class, 'codigo_interno', null],
        'controlEstadisticosServicios' => [parent::class, 'controlEstadisticosServicios', null],
        'descripcion' => [parent::class, 'descripcion', null],
        'estado' => [parent::class, 'estado', null],
        'fecha_creacion' => [parent::class, 'fecha_creacion', null],
        'fecha_modificacion' => [parent::class, 'fecha_modificacion', null],
        'horario' => [parent::class, 'horario', null],
        'id' => [parent::class, 'id', null],
        'idHorario' => [parent::class, 'idHorario', null],
        'idServiciosProductosDetalles' => [parent::class, 'idServiciosProductosDetalles', null],
        'idZonaUsuario' => [parent::class, 'idZonaUsuario', null],
        'nombre' => [parent::class, 'nombre', null],
        'serviciosProductosDetalles' => [parent::class, 'serviciosProductosDetalles', null],
        'zonaUsuario' => [parent::class, 'zonaUsuario', null],
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
