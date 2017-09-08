<?php

namespace Vierbeuter\WordPress;

use Vierbeuter\WordPress\Traits\HasDependencyInjectionContainer;
use Vierbeuter\WordPress\Traits\HasFeatureSupport;
use Vierbeuter\WordPress\Traits\HasPluginData;
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
     * include properties and methods for retrieving general plugin data
     */
    use HasPluginData;
    /**
     * include methods for DI-container support
     */
    use HasDependencyInjectionContainer;
    /**
     * include methods for translating texts
     */
    use HasTranslatorService;
    /**
     * include methods for working with features
     */
    use HasFeatureSupport;

    /**
     * Plugin constructor.
     *
     * @param string $pluginFile
     */
    private function __construct(string $pluginFile)
    {
        //  set plugin file and some other data
        $this->setPluginFile($pluginFile);

        //  initialize service container
        $this->initDiContainer();

        //  initialize services
        $this->addTranslator();

        //  initialize features etc.
        $this->initPlugin();
    }

    /**
     * Activates the plugin. Expects an absolute file path to the WordPress plugin's index.php file.
     *
     * Example usge in "your-awesome-plugin/index.php":
     * <code>
     * YourAwesomePlugin::activate(__FILE__);
     * </code>
     *
     * @param string $pluginFile
     */
    public static function activate(string $pluginFile)
    {
        //  only activate a plugin once
        if (empty(static::$plugin)) {
            //  construct new instance to initialize and activate the plugin
            static::$plugin = new static($pluginFile);
        }
    }

    /**
     * Returns the plugin unless it's not activated.
     *
     * @return \Vierbeuter\WordPress\Plugin
     *
     * @throws \Exception if plugin is not activated
     *
     * @see \Vierbeuter\WordPress\Plugin::activate()
     */
    public static function getInstance(): Plugin
    {
        if (empty(static::$plugin)) {
            throw new \Exception('Plugin not activated, invoke activate(…) method first before using it.');
        }

        return static::$plugin;
    }

    /**
     * Initializes the plugin, e.g. adds features or services using the addFeature(…) and addComponent(…) methods.
     *
     * Example implementation:
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
