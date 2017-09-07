<?php

namespace Vierbeuter\WordPress\Traits;

use Pimple\Container;

/**
 * The HasDependencyInjectionContainer trait provides methods for accessing a DI-container to store services in, for
 * example.
 *
 * @package Vierbeuter\WordPress\Traits
 */
trait HasDependencyInjectionContainer
{

    /**
     * @var \Pimple\Container
     *
     * container to be used for dependeny injection
     *
     * @see https://pimple.symfony.com/
     */
    protected $container;

    /**
     * Initializes the DI-container to store any components in such as services.
     */
    private function initDiContainer(): void
    {
        $this->container = new Container();
    }

    /**
     * Adds the given component to the DI-container.
     *
     * @param string $name
     * @param mixed $instance
     */
    protected function addComponent(string $name, mixed $instance): void
    {
        /**
         * @param \Pimple\Container $c
         *
         * @return mixed
         */
        $this->container[$name] = function (Container $c) use ($instance) {
            return $instance;
        };
    }

    /**
     * Returns the component for given name.
     *
     * @param string $name
     *
     * @return mixed
     */
    protected function getComponent(string $name): mixed
    {
        return $this->container[$name];
    }
}
