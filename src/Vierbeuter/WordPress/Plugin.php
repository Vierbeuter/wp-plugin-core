<?php

namespace Vierbeuter\WordPress;

use Vierbeuter\WordPress\Di\Component;
use Vierbeuter\WordPress\Di\Container;
use Vierbeuter\WordPress\Traits\HasFeatureSupport;
use Vierbeuter\WordPress\Traits\HasPluginData;
use Vierbeuter\WordPress\Traits\HasTranslatorService;

/**
 * The Plugin class is supposed to be extended by any class representing a concrete plugin. It provides core
 * functionality for implementing WordPress plugins.
 */
abstract class Plugin extends Component
{

    /**
     * @var \Vierbeuter\WordPress\Plugin[]
     *
     * the WordPress plugin itself
     */
    protected static $plugins;

    /**
     * include properties and methods for retrieving general plugin data
     */
    use HasPluginData;
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
     * @param array $parameters
     */
    private function __construct(string $pluginFile, array $parameters = [])
    {
        //  set plugin file and some other data
        $this->setPluginFile($pluginFile);

        //  initialize service container
        $this->initDiContainer($parameters);

        //  initialize services
        $this->addTranslator();

        //  initialize features etc.
        $this->initPlugin();
    }

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
     * Activates the plugin. Expects an absolute file path of the WordPress plugin's index.php file to be passed.
     *
     * Example usage within "your-awesome-plugin/index.php" (after registering an autoloader):
     * <code>
     * \Any\Namespace\YourAwesomePlugin::activate(__FILE__);
     * </code>
     *
     * @param string $pluginFile absolute path of the WordPress plugin's index.php file.
     * @param array $parameters parameters to be passed to the plugin, e.g. configurations to be accessed on
     *     initializing the plugin features etc.
     *
     * @see \Vierbeuter\WordPress\Autoloader::register()
     */
    public static function activate(string $pluginFile, array $parameters = [])
    {
        //  only activate a plugin once
        if (empty(static::$plugins[get_called_class()])) {
            //  construct new instance to initialize and activate the plugin
            static::$plugins[get_called_class()] = new static($pluginFile, $parameters);
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
        if (empty(static::$plugins[get_called_class()])) {
            throw new \Exception('Plugin not activated, invoke activate(…) method first before using it.');
        }

        return static::$plugins[get_called_class()];
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
     *   //  add another awesome feature, but one a parameter is passed to
     *   $this->addFeature(new AwesomeFeatureWithParam($this->getParameter('my-awesome-param')));
     *   //  register an awesome service to DI-container
     *   $this->addComponent('service-handle', new AwesomeService());
     *   //  register an awesome service with parameter
     *   $this->addComponent('service-handle', new AwesomeParameterizedService($this->getParameter('param')));
     * }
     * </code>
     *
     * @see \Vierbeuter\WordPress\Plugin::addFeature()
     * @see \Vierbeuter\WordPress\Plugin::addComponent()
     * @see \Vierbeuter\WordPress\Plugin::getParameter()
     */
    abstract protected function initPlugin(): void;
}
