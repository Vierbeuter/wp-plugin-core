<?php

namespace Vierbeuter\WordPress\Traits;

use Pimple\Container;
use Vierbeuter\WordPress\Component;

/**
 * The HasDependencyInjectionContainer trait provides methods for accessing a DI-container to store any components in
 * such as services, for example.
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
     * @param \Vierbeuter\WordPress\Component $component
     */
    protected function addComponent(string $name, Component $component): void
    {
        /**
         * @param \Pimple\Container $c
         *
         * @return \Vierbeuter\WordPress\Component
         */
        $this->container[$name] = function (Container $c) use ($component) {
            return $component;
        };
    }

    /**
     * Returns the component for given name.
     *
     * @param string $name
     *
     * @return null|\Vierbeuter\WordPress\Component
     */
    protected function getComponent(string $name): ?Component
    {
        return isset($this->container[$name]) ? $this->container[$name] : null;
    }
}
