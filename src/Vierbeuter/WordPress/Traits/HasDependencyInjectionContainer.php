<?php

namespace Vierbeuter\WordPress\Traits;

use Vierbeuter\WordPress\Di\Container;

/**
 * The HasDependencyInjectionContainer trait provides methods for accessing a DI-container to store any components in
 * such as services, for example.
 *
 * @package Vierbeuter\WordPress\Traits
 */
trait HasDependencyInjectionContainer
{

    /**
     * @var \Vierbeuter\WordPress\Di\Container
     *
     * container to be used for dependeny injection
     *
     * @see https://pimple.symfony.com/
     */
    protected $container;

    /**
     * Initializes the DI-container to store any components in such as services.
     *
     * @param array $parameters parameters to be stored in the container
     */
    private function initDiContainer(array $parameters = []): void
    {
        $this->container = new Container();

        //  add parameters to container
        foreach ($parameters as $paramKey => $paramValue) {
            $this->addParameter($paramKey, $paramValue);
        }
    }
}
