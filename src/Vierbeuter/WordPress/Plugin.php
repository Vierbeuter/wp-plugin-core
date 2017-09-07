<?php

namespace Vierbeuter\WordPress;

use Vierbeuter\WordPress\Traits\HasDependencyInjectionContainer;
use Vierbeuter\WordPress\Traits\HasFeatureSupport;
use Vierbeuter\WordPress\Traits\HasTranslatorService;

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
     * include methods for DI-container support
     */
    use HasDependencyInjectionContainer;
    /**
     * include methods for translating texts
     */
    use HasTranslatorService;
    /**
     * include methods for working with feaetures
     */
    use HasFeatureSupport;

    /**
     * Plugin constructor.
     */
    private function __construct()
    {
        //  initialize service container
        $this->initDiContainer();

        //  initialize services
        $this->addTranslator();

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
     * Initializes the plugin, e.g. adds features or services using the addFeature(…) and addComponent(…) methods.
     *
     * Example implemantation:
     *
     * <code>
     * protected function initPlugin(): void
     * {
     *   //  add an awesome feature
     *   $this->addFeature(new AwesomeFeature());
     *   //  register an awesome service to DI-container
     *   $this->addComponent('service-handle', new AwesomeService());
     * }
     * </code>
     *
     * @see \Vierbeuter\WordPress\Plugin::addFeature()
     * @see \Vierbeuter\WordPress\Plugin::addComponent()
     */
    abstract protected function initPlugin(): void;
}
