<?php

namespace Vierbeuter\WordPress;

use Vierbeuter\WordPress\Di\Container;
use Vierbeuter\WordPress\Service\CoreTranslator;
use Vierbeuter\WordPress\Service\PluginTranslator;
use Vierbeuter\WordPress\Service\WpmlWpOptions;
use Vierbeuter\WordPress\Service\WpOptions;

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
     * @var \Vierbeuter\WordPress\Plugin
     */
    protected $plugin;

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
     * @throws \Exception if a plugin has already been activated by this registrar
     *
     * @see \Vierbeuter\WordPress\Plugin
     */
    public function activate(string $className, array $parameters = []): void
    {
        //  ensure to activate plugin only once because this class is designed to register and activate only one
        //  plugin at a time … otherwise two plugins might share the same DI-container which would be followed by
        //  several problems (there's only one plugin-file parameter, the same translator would have to handle
        //  different domains)
        if (!empty($this->plugin)) {
            throw new \Exception('Please create a new PluginRegistrar to activate the plugin "' . $className . '".');
        }

        //  check given class name to ensure it exists and it's a sub-class of Plugin
        if (empty($className) || !class_exists($className) || !is_subclass_of($className, Plugin::class)) {
            throw new \InvalidArgumentException('Invalid class name given: "' . $className . '". Please provide the name (including namespace) of an existing sub-class of "' . Plugin::class . '".');
        }

        //  add components and parameters to DI-container
        $this->addComponents($className);
        $this->addParameters($parameters);

        //  get the plugin and keep it in mind for accessing it later on
        $this->plugin = $this->container->getComponent($className);
        //  initialize the plugin's features etc.
        $this->plugin->initPlugin();
    }

    /**
     * Returns the plugin.
     *
     * @return \Vierbeuter\WordPress\Plugin
     *
     * @throws \Exception if no valid plugin has been activated yet using this registrar's activate(…) method
     *
     * @see \Vierbeuter\WordPress\PluginRegistrar::activate()
     */
    public function getPlugin(): Plugin
    {
        if (empty($this->plugin)) {
            throw new \Exception('No plugin found. Please invoke activate() method first.');
        }

        return $this->plugin;
    }

    /**
     * Adds all must-have components of the plugin to the DI-container like the plugin itself, the plugin-data, the
     * translators and so on.
     *
     * @param string $pluginClassName
     */
    protected function addComponents(string $pluginClassName)
    {
        //  add class name of actual plugin
        $this->container->addComponent($pluginClassName);
        //  add plugin-data
        $this->container->addComponent(PluginData::class, static::PARAM_PLUGIN_FILE);

        //  add translator for the actual plugin
        $this->container->addComponent(PluginTranslator::class, PluginData::class);
        //  add translator for the base classes / plugin core
        $this->container->addComponent(CoreTranslator::class);

        //  add wp_options service
        $this->container->addComponent(WpOptions::class, PluginData::class);
        //  add wp_options service supporting WPML
        $this->container->addComponent(WpmlWpOptions::class, PluginData::class);
    }

    /**
     * Initializes the DI-container to store any components in such as services.
     *
     * @param array $parameters parameters to be stored in the container
     */
    protected function addParameters(array $parameters = []): void
    {
        //  add parameters to container
        foreach ($parameters as $paramKey => $paramValue) {
            $this->container->addParameter($paramKey, $paramValue);
        }
    }
}
