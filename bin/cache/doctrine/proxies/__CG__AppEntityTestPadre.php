<?php

namespace Proxies\__CG__\App\Entity\Test;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Padre extends \App\Entity\Test\Padre implements \Doctrine\ORM\Proxy\InternalProxy
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
        "\0".parent::class."\0".'estado' => [parent::class, 'estado', null],
        "\0".parent::class."\0".'hijo1' => [parent::class, 'hijo1', null],
        "\0".parent::class."\0".'hijo2' => [parent::class, 'hijo2', null],
        "\0".parent::class."\0".'id' => [parent::class, 'id', null],
        "\0".parent::class."\0".'nombre' => [parent::class, 'nombre', null],
        'codigo_interno' => [parent::class, 'codigo_interno', null],
        'estado' => [parent::class, 'estado', null],
        'hijo1' => [parent::class, 'hijo1', null],
        'hijo2' => [parent::class, 'hijo2', null],
        'id' => [parent::class, 'id', null],
        'nombre' => [parent::class, 'nombre', null],
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
