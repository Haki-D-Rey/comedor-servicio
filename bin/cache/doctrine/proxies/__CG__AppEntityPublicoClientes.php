<?php

namespace Proxies\__CG__\App\Entity\Publico;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Clientes extends \App\Entity\Publico\Clientes implements \Doctrine\ORM\Proxy\InternalProxy
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
        "\0".parent::class."\0".'apellidos' => [parent::class, 'apellidos', null],
        "\0".parent::class."\0".'cargo' => [parent::class, 'cargo', null],
        "\0".parent::class."\0".'clieDocnum' => [parent::class, 'clieDocnum', null],
        "\0".parent::class."\0".'correo' => [parent::class, 'correo', null],
        "\0".parent::class."\0".'departamento' => [parent::class, 'departamento', null],
        "\0".parent::class."\0".'estado' => [parent::class, 'estado', null],
        "\0".parent::class."\0".'fechaCreacion' => [parent::class, 'fechaCreacion', null],
        "\0".parent::class."\0".'fechaModificacion' => [parent::class, 'fechaModificacion', null],
        "\0".parent::class."\0".'id' => [parent::class, 'id', null],
        "\0".parent::class."\0".'nombres' => [parent::class, 'nombres', null],
        'apellidos' => [parent::class, 'apellidos', null],
        'cargo' => [parent::class, 'cargo', null],
        'clieDocnum' => [parent::class, 'clieDocnum', null],
        'correo' => [parent::class, 'correo', null],
        'departamento' => [parent::class, 'departamento', null],
        'estado' => [parent::class, 'estado', null],
        'fechaCreacion' => [parent::class, 'fechaCreacion', null],
        'fechaModificacion' => [parent::class, 'fechaModificacion', null],
        'id' => [parent::class, 'id', null],
        'nombres' => [parent::class, 'nombres', null],
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
