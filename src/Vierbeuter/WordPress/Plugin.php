<?php

namespace Vierbeuter\WordPress;

use Vierbeuter\WordPress\Di\Component;
use Vierbeuter\WordPress\Di\Container;
use Vierbeuter\WordPress\Traits\HasFeatureSupport;
use Vierbeuter\WordPress\Traits\HasTranslatorSupport;

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
     * include methods for translating texts
     */
    use HasTranslatorSupport;
    /**
     * include methods for working with features
     */
    use HasFeatureSupport;

    /**
     * Plugin constructor.
     *
     * @param \Vierbeuter\WordPress\Di\Container $container
     */
    private function __construct(Container $container)
    {
        //  set DI-container to be used for all features and stuff
        $this->setContainer($container);

        //  initialize features etc.
        $this->initPlugin();
    }

    /**
     * Activates the plugin. Expects an absolute file path of the WordPress plugin's index.php file to be passed.
     *
     * Example usage within "your-awesome-plugin/index.php" (after registering an autoloader):
     * <code>
     * \Any\Namespace\YourAwesomePlugin::activate(__FILE__);
     * </code>
     *
     * @param \Vierbeuter\WordPress\Di\Container $container
     *
     * @see \Vierbeuter\WordPress\Autoloader::register()
     */
    public static function activate(Container $container)
    {
        //  TODO: make method non-static

        //  only activate a plugin once
        if (empty(static::$plugins[get_called_class()])) {
            //  construct new instance to initialize and activate the plugin
            static::$plugins[get_called_class()] = new static($container);
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
     * Example implementations and explanations:
     *
     * <code>
     * protected function initPlugin(): void
     * {
     *   //  optionally add some parameters (may also be passed to Plugin constructor as associative array)
     *   $this->addParameter('my-awesome-param', 'awesome-value');
     *   $this->addParameter('param', 'value');
     *
     *   //  # 1
     *
     *   //  add an awesome feature
     *   $this->addFeature(new AwesomeFeature());
     *   //  add another awesome feature, but one a parameter is passed to
     *   $this->addFeature(new AwesomeFeatureWithParam($this->getParameter('my-awesome-param')));
     *
     *   //  # 2
     *
     *   //  register an awesome service to DI-container
     *   $this->addComponent(new AwesomeService());
     *   //  register an awesome service with parameter
     *   $this->addComponent(new AwesomeParameterizedService($this->getParameter('param')));
     *   //  register an awesome service with parameter; alternative notation (using dependency injection)
     *   $this->addComponent(AwesomeParameterizedService::class, 'param');
     *
     *   //  # 3
     *
     *   //  register a service (component) that other components are passed to
     *   $this->addComponent(new AwesomeService(new AnyComponent(), new OtherComponent()));
     *
     *   //  # 4
     *
     *   //  register the same component making use of dependency injection
     *   $this->addComponent(AwesomeService::class, AnyComponent::class, OtherComponent::class);
     *   //  NOTE:
     *   //  the parameter list of addComponent(…) is dependant on the constructor's parameter signature
     *   //  of AwesomeService (so, here we assume its 1st parameter is expected to bean instance of
     *   //  AnyComponent and the 2nd one is of type OtherComponent)
     *
     *   //  also ensure the passed components are added to the DI-container
     *   //  choose either notation (but latter one recommended)
     *   $this->addComponent(new OtherComponent());
     *   $this->addComponent(AnyComponent::class);
     * }
     * </code>
     *
     * Please mention that the order of adding components (such as services) is unimportant when using dependency
     * injection since components will be created and loaded at a later time.
     *
     * Passing a class name to the addComponent(…) method is the recommended way, of course. Unless you have to do it
     * (for whatever reason this may be) avoid instantiating your components and adding these to the DI-container.
     *
     * @see \Vierbeuter\WordPress\Plugin::addFeature()
     * @see \Vierbeuter\WordPress\Plugin::addComponent()
     * @see \Vierbeuter\WordPress\Plugin::getParameter()
     */
    abstract protected function initPlugin(): void;
}
