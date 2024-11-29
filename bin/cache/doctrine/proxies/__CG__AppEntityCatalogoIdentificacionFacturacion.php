<?php

namespace Proxies\__CG__\App\Entity\Catalogo;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class IdentificacionFacturacion extends \App\Entity\Catalogo\IdentificacionFacturacion implements \Doctrine\ORM\Proxy\InternalProxy
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
        "\0".parent::class."\0".'codigoInterno' => [parent::class, 'codigoInterno', null],
        "\0".parent::class."\0".'descripcion' => [parent::class, 'descripcion', null],
        "\0".parent::class."\0".'estado' => [parent::class, 'estado', null],
        "\0".parent::class."\0".'fechaCreacion' => [parent::class, 'fechaCreacion', null],
        "\0".parent::class."\0".'fechaModificacion' => [parent::class, 'fechaModificacion', null],
        "\0".parent::class."\0".'id' => [parent::class, 'id', null],
        "\0".parent::class."\0".'nombre' => [parent::class, 'nombre', null],
        'codigoInterno' => [parent::class, 'codigoInterno', null],
        'descripcion' => [parent::class, 'descripcion', null],
        'estado' => [parent::class, 'estado', null],
        'fechaCreacion' => [parent::class, 'fechaCreacion', null],
        'fechaModificacion' => [parent::class, 'fechaModificacion', null],
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
