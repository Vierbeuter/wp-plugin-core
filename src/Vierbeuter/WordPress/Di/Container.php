<?php

namespace Vierbeuter\WordPress\Di;

use Pimple\Container as Pimple_Container;

/**
 * The Container class is used for dependency injection. It's based on the Pimple Container.
 *
 * This DI-container can handle components to be added to and accessed from.
 *
 * @package Vierbeuter\WordPress\Di
 *
 * @see \Vierbeuter\WordPress\Di\Component
 * @see https://pimple.symfony.com/
 */
class Container extends Pimple_Container
{

    /**
     * Adds the given component to the DI-container.
     *
     * @param string $componentClass class name of component to be added, the class has to be a sub-class of Component
     * @param array $paramNames names of parameters to be passed to the component's constructor, the parameters are
     *     expected to be found in the DI-containter as well, ensure they are added before accessing the given component
     *
     * @see \Vierbeuter\WordPress\Di\Component
     * @see https://pimple.symfony.com/#defining-services
     */
    public function addComponent(string $componentClass, ...$paramNames): void
    {
        //  component class must be non-empty
        if (empty($componentClass)) {
            throw new \InvalidArgumentException('First parameter may not be empty.');
        }

        //  check given class name
        if (class_exists($componentClass) && is_subclass_of($componentClass, Component::class)) {
            /**
             * @param \Vierbeuter\WordPress\Di\Container $c
             *
             * @return \Vierbeuter\WordPress\Di\Component
             */
            $this[$componentClass] = function (Container $c) use ($componentClass, $paramNames) {
                //  extract other components and parameters using the DI-container
                $params = array_map(function (string $paramName) use ($c, $componentClass) {
                    if (!isset($c[$paramName])) {
                        $message = 'Cannot instantiate "' . $componentClass . '", parameter "' . $paramName . '" not found in DI-container.';
                        throw new \InvalidArgumentException($message);
                    }

                    return $c[$paramName];
                }, $paramNames);

                //  instantiate component of given class and pass the parameters
                /** @var \Vierbeuter\WordPress\Di\Component $component */
                $component = new $componentClass(...$params);
                //  set container to component
                $component->setContainer($c);

                //  return the component to be stored into container
                return $component;
            };
        } else {
            $message = 'Cannot instantiate "' . $componentClass . '", the class name is expected to be an existing sub-class of the Component class.';
            throw new \InvalidArgumentException($message);
        }
    }

    /**
     * Returns the component for given name.
     *
     * @param string $name class name of the component to be returned
     *
     * @return null|\Vierbeuter\WordPress\Di\Component
     */
    public function getComponent(string $name): ?Component
    {
        return isset($this[$name]) ? $this[$name] : null;
    }

    /**
     * Adds the given parameter to the DI-container.
     *
     * @param string $name parameter name the value has to be accessible with
     * @param mixed $value parameter value to be stored
     *
     * @see https://pimple.symfony.com/#defining-parameters
     */
    public function addParameter(string $name, $value): void
    {
        $this[$name] = $value;
    }

    /**
     * Returns the parameter for given name.
     *
     * @param string $name name of the parameter value to be returned
     *
     * @return null|mixed
     */
    public function getParameter(string $name)
    {
        return isset($this[$name]) ? $this[$name] : null;
    }
}
