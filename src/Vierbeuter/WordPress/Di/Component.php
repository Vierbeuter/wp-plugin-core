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
}
