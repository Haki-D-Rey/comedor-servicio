<?php

namespace Proxies\__CG__\App\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Sistemas extends \App\Entity\Sistemas implements \Doctrine\ORM\Proxy\InternalProxy
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
        "\0".parent::class."\0".'descripcion' => [parent::class, 'descripcion', null],
        "\0".parent::class."\0".'estado' => [parent::class, 'estado', null],
        "\0".parent::class."\0".'fecha_creacion' => [parent::class, 'fecha_creacion', null],
        "\0".parent::class."\0".'fecha_modificacion' => [parent::class, 'fecha_modificacion', null],
        "\0".parent::class."\0".'id' => [parent::class, 'id', null],
        "\0".parent::class."\0".'nombre' => [parent::class, 'nombre', null],
        "\0".parent::class."\0".'serviciosProductosDetalles' => [parent::class, 'serviciosProductosDetalles', null],
        'codigo_interno' => [parent::class, 'codigo_interno', null],
        'descripcion' => [parent::class, 'descripcion', null],
        'estado' => [parent::class, 'estado', null],
        'fecha_creacion' => [parent::class, 'fecha_creacion', null],
        'fecha_modificacion' => [parent::class, 'fecha_modificacion', null],
        'id' => [parent::class, 'id', null],
        'nombre' => [parent::class, 'nombre', null],
        'serviciosProductosDetalles' => [parent::class, 'serviciosProductosDetalles', null],
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