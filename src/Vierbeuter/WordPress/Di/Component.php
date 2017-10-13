<?php

namespace Vierbeuter\WordPress\Di;

/**
 * A Component can be nearly anything.
 *
 * Components provide DI-container support, they can be added to the container and they can access the container on
 * their own.
 *
 * @package Vierbeuter\WordPress
 *
 * @see \Vierbeuter\WordPress\Di\Container
 */
abstract class Component
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
     * Returns the container.
     *
     * @return \Vierbeuter\WordPress\Di\Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Sets the container.
     *
     * @param \Vierbeuter\WordPress\Di\Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Adds the given component to the DI-container.
     *
     * @param string $componentClass class name of component to be added, the class has to be a sub-class of Component
     * @param array $paramNames names of parameters to be passed to the component's constructor, the parameters are
     *     expected to be found in the DI-containter as well, ensure they are added before accessing the given component
     *
     * @see \Vierbeuter\WordPress\Di\Component
     */
    public function addComponent(string $componentClass, ...$paramNames): void
    {
        $this->container->addComponent($componentClass, ...$paramNames);
    }

    /**
     * Returns the component for given name.
     *
     * @param string $name class name of the component to be returned
     *
     * @return null|\Vierbeuter\WordPress\Di\Component
     */
    public function getComponent(string $name): ?Component
    {
        return isset($this->container[$name]) ? $this->container[$name] : null;
    }

    /**
     * Adds the given parameter to the DI-container.
     *
     * @param string $name parameter name the value has to be accessible with
     * @param mixed $value parameter value to be stored
     *
     * @see https://pimple.symfony.com/#defining-parameters
     */
    public function addParameter(string $name, $value): void
    {
        $this->container->addParameter($name, $value);
    }

    /**
     * Returns the parameter for given name.
     *
     * @param string $name name of the parameter value to be returned
     *
     * @return null|mixed
     */
    public function getParameter(string $name)
    {
        return $this->container->getParameter($name);
    }
}
