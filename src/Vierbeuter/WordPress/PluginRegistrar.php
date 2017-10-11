<?php

namespace Vierbeuter\WordPress;

use Vierbeuter\WordPress\Di\Container;

/**
 * The PluginRegistrar registers and activates WordPress plugins built upon `wp-plugin-core` library.
 *
 * It is required to be called from within the plugin's index.php file to correctly determine file paths.
 *
 * Sample usage:
 * <code>
 * //  within your-awesome-plugin/index.php
 * $registrar = new \Vierbeuter\WordPress\PluginRegistrar();
 * $registrar->activate(\YourAwesomeCompany\AnyNamespace\YourAwesomePlugin::class);
 * </code>
 *
 * @package Vierbeuter\WordPress
 */
class PluginRegistrar
{

    /**
     * the DI-container's parameter key holding an absolute path of the WordPress plugin's index.php file
     */
    const PARAM_PLUGIN_FILE = 'plugin-file';

    /**
     * @var \Vierbeuter\WordPress\Di\Container
     */
    protected $container;

    /**
     * PluginRegistrar constructor.
     */
    public function __construct()
    {
        //  first of all create the DI-container to be used by the plugin
        $this->container = new Container();

        //  get curent backtrace (with limit of 1 to only get the caller of this class constructor)
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        //  first element is the caller (a plugin's index.php file)
        $caller = reset($backtrace);
        //  get the plugin file path (as if from above caller the magic constant __FILE__ is used)
        $pluginFile = $caller['file'];

        //  register an autoloader for current plugin to be able to load all classes and the plugin itself
        Autoloader::register($pluginFile);

        //  add plugin file path to container to be accessed later on
        $this->container->addParameter(static::PARAM_PLUGIN_FILE, $pluginFile);
    }

    /**
     * Activates the given plugin.
     *
     * @param string $className class name of the plugin to be activated, must be an existing sub-class of Plugin
     * @param array $parameters parameters to be passed to the plugin, e.g. configurations to be accessed on
     *     initializing the plugin features etc.
     *
     * @see \Vierbeuter\WordPress\Plugin
     */
    public function activate(string $className, array $parameters = []): void
    {
        //  check given class name to ensure it exists and it's a sub-class of Plugin
        if (empty($className) || !class_exists($className) || !is_subclass_of($className, Plugin::class)) {
            throw new \InvalidArgumentException('Invalid class name given: "' . $className . '". Please provide the name (including namespace) of an existing sub-class of "' . Plugin::class . '".');
        }

        //  TODO: replace static method call with DI and call of non-static activate()-method

        /** @see \Vierbeuter\WordPress\Plugin::activate() */
        $className::activate($this->container, $parameters);
    }
}
