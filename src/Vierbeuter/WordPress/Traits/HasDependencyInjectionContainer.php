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
     * key prefix to be used for components added to the DI-container
     *
     * @var string
     *
     * @see \Vierbeuter\WordPress\Traits\HasDependencyInjectionContainer::getComponentKey()
     */
    private $prefixComponent = 'component_';

    /**
     * key prefix to be used for parameters added to the DI-container
     *
     * @var string
     *
     * @see \Vierbeuter\WordPress\Traits\HasDependencyInjectionContainer::getParameterKey()
     */
    private $prefixParameter = 'parameter_';

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

    /**
     * Adds the given component to the DI-container.
     *
     * @param string $name
     * @param \Vierbeuter\WordPress\Component $component
     *
     * @see https://pimple.symfony.com/#defining-services
     */
    protected function addComponent(string $name, Component $component): void
    {
        $key = $this->getComponentKey($name);

        /**
         * @param \Pimple\Container $c
         *
         * @return \Vierbeuter\WordPress\Component
         */
        $this->container[$key] = function (Container $c) use ($component) {
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
        $key = $this->getComponentKey($name);

        return isset($this->container[$key]) ? $this->container[$key] : null;
    }

    /**
     * Returns the container key for given component name.
     *
     * @param string $name
     *
     * @return string
     *
     * @see \Vierbeuter\WordPress\Traits\HasDependencyInjectionContainer::$prefixComponent
     */
    private function getComponentKey(string $name): string
    {
        return $this->prefixComponent . $name;
    }

    /**
     * Adds the given parameter to the DI-container.
     *
     * @param string $name
     * @param mixed $value
     *
     * @see https://pimple.symfony.com/#defining-parameters
     */
    protected function addParameter(string $name, $value): void
    {
        $key = $this->getParameterKey($name);
        $this->container[$key] = $value;
    }

    /**
     * Returns the parameter for given name.
     *
     * @param string $name
     *
     * @return null|mixed
     */
    protected function getParameter(string $name)
    {
        $key = $this->getParameterKey($name);

        return isset($this->container[$key]) ? $this->container[$key] : null;
    }

    /**
     * Returns the container key for given parameter name.
     *
     * @param string $name
     *
     * @return string
     *
     * @see \Vierbeuter\WordPress\Traits\HasDependencyInjectionContainer::$prefixParameter
     */
    private function getParameterKey(string $name): string
    {
        return $this->prefixParameter . $name;
    }
}
