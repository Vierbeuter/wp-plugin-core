<?php

namespace Vierbeuter\WordPress\Traits;

use Vierbeuter\WordPress\Di\Component;
use Vierbeuter\WordPress\Di\Container;

/**
 * The HasDependencyInjectionContainer trait provides methods for accessing a DI-container to store any components in
 * such as services, for example.
 *
 * @package Vierbeuter\WordPress\Traits
 */
trait HasDependencyInjectionContainer
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
     * Adds the given component to the DI-container.
     *
     * @param \Vierbeuter\WordPress\Di\Component|string $componentOrClassName component or its class name to be added,
     *     class has to be a sub-class of Component
     * @param array $paramNames names of parameters to be passed to the component's constructor, the parameters are
     *     expected to be found in the DI-containter as well, ensure they are added before accessing the given component
     *
     * @see \Vierbeuter\WordPress\Di\Component
     */
    protected function addComponent($componentOrClassName, ...$paramNames): void
    {
        //  check given component
        switch (true) {

            //  component must be non-empty
            case empty($componentOrClassName):
                throw new \InvalidArgumentException('First parameter may not be empty.');

            //  given component is a string --> seems to be a class name
            case is_string($componentOrClassName):
                $this->addComponentByClassName($componentOrClassName, ...$paramNames);
                break;

            //  given component is an object of type Component --> just add it
            case is_object($componentOrClassName) && $componentOrClassName instanceof Component:
                $this->addComponentByInstance($componentOrClassName);
                break;

            //  given component is not what it's expected to be --> meh
            default:
                throw new \InvalidArgumentException('Expected an instance of a sub-class of Component or its class name and an optional parameter list, but ' . gettype($componentOrClassName) . ' given: "' . $componentOrClassName . '".');
        }
    }

    /**
     * Adds the given component to the DI-container.
     *
     * @param string $className class name of component to be added, class has to be a sub-class of Component
     * @param array $paramNames names of parameters to be passed to the component's constructor, the parameters are
     *     expected to be found in the DI-containter as well, ensure they are added
     *
     * @see \Vierbeuter\WordPress\Di\Component
     * @see https://pimple.symfony.com/#defining-services
     */
    private function addComponentByClassName(string $className, ...$paramNames): void
    {
        //  check given class name
        if (class_exists($className) && is_subclass_of($className, Component::class)) {
            /**
             * @param \Vierbeuter\WordPress\Di\Container $c
             *
             * @return \Vierbeuter\WordPress\Di\Component
             */
            $this->container[$className] = function (Container $c) use ($className, $paramNames) {
                //  extract other components and parameters using the DI-container
                $params = array_map(function (string $paramName) use ($c, $className) {
                    if (!isset($c[$paramName])) {
                        $message = 'Cannot instantiate "' . $className . '", parameter "' . $paramName . '" not found in DI-container.';
                        throw new \InvalidArgumentException($message);
                    }

                    return $c[$paramName];
                }, $paramNames);

                //  instantiate component of given class and pass the parameters
                /** @var \Vierbeuter\WordPress\Di\Component $component */
                $component = new $className(...$params);
                //  set container to component
                $component->setContainer($c);

                //  return the component to be stored into container
                return $component;
            };
        } else {
            $message = 'Cannot instantiate "' . $className . '", the class name is expected to be an existing sub-class of the Component class.';
            throw new \InvalidArgumentException($message);
        }
    }

    /**
     * Adds the given component to the DI-container.
     *
     * @param \Vierbeuter\WordPress\Di\Component $component component to be added
     *
     * @see \Vierbeuter\WordPress\Di\Component
     * @see https://pimple.symfony.com/#defining-services
     */
    private function addComponentByInstance(Component $component): void
    {
        /**
         * @param \Vierbeuter\WordPress\Di\Container $c
         *
         * @return \Vierbeuter\WordPress\Di\Component
         */
        $this->container[get_class($component)] = function (Container $c) use ($component) {
            //  set container to component
            $component->setContainer($c);

            //  return the component to be stored into container
            return $component;
        };
    }

    /**
     * Returns the component for given name.
     *
     * @param string $name
     *
     * @return null|\Vierbeuter\WordPress\Di\Component
     */
    protected function getComponent(string $name): ?Component
    {
        return isset($this->container[$name]) ? $this->container[$name] : null;
    }

    /**
     * Adds the given parameter to the DI-container.
     *
     * @param string $name
     * @param mixed $value
     *
     * @see https://pimple.symfony.com/#defining-parameters
     */
    protected function addParameter(string $name, $value): void
    {
        $this->container[$name] = $value;
    }

    /**
     * Returns the parameter for given name.
     *
     * @param string $name
     *
     * @return null|mixed
     */
    protected function getParameter(string $name)
    {
        return isset($this->container[$name]) ? $this->container[$name] : null;
    }
}
