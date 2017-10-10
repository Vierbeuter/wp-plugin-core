<?php

namespace Vierbeuter\WordPress\Di;

use Pimple\Container;

/**
 * A Component can be nearly anything.
 *
 * Components provide DI-container support, they can be added to the container and they can access the container on
 * their own.
 *
 * @package Vierbeuter\WordPress
 */
abstract class Component
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
     * Returns the container.
     *
     * @return \Pimple\Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Sets the container.
     *
     * @param \Pimple\Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }
}
