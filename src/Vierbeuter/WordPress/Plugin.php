<?php

namespace Vierbeuter\WordPress;

use Pimple\Container;
use Vierbeuter\WordPress\Service\Translator;

/**
 * The Plugin class is supposed to be extended by any class representing a concrete plugin. It provides core
 * functionality for implementing WordPress plugins.
 */
abstract class Plugin
{

    /**
     * @var \Vierbeuter\WordPress\Plugin
     *
     * the WordPress plugin itself
     */
    protected static $plugin;

    /**
     * @var \Pimple\Container
     *
     * container to be used for dependeny injection
     *
     * @see https://pimple.symfony.com/
     */
    protected $container;

    /**
     * Plugin constructor.
     */
    private function __construct()
    {
        $this->container = new Container();

        //  initialize services
        $this->addComponent('translator', new Translator());

        //  initialize features etc.
        $this->initPlugin();
    }

    /**
     * Returns the plugin.
     *
     * @return \Vierbeuter\WordPress\Plugin
     */
    public static function getInstance(): Plugin
    {
        if (empty(static::$plugin)) {
            static::$plugin = new static();
        }

        return static::$plugin;
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
        $this->container[$name] = function ($c) use ($instance) {
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

    /**
     * Translates the given text.
     *
     * @param string $text
     *
     * @return string
     */
    public function translate(string $text): string
    {
        return $this->getComponent('translator')->translate($text);
    }

    /**
     * Initializes the plugin, e.g. adds features or services using the addFeature(…) and addComponent(…) methods.
     *
     * Example implemantation:
     *
     * <code>
     * protected function initPlugin(): void
     * {
     *   //  register an awesome service to DI-container
     *   $this->addComponent('service-handle', new AwesomeService());
     * }
     * </code>
     *
     * @see \Vierbeuter\WordPress\Plugin::addComponent()
     */
    abstract protected function initPlugin(): void;
}
