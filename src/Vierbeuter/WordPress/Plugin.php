<?php

namespace Vierbeuter\WordPress;

use Vierbeuter\WordPress\Di\Component;
use Vierbeuter\WordPress\Traits\HasFeatureSupport;
use Vierbeuter\WordPress\Traits\HasTranslatorSupport;

/**
 * The Plugin class is supposed to be extended by any class representing a concrete plugin. It provides core
 * functionality for implementing WordPress plugins.
 */
abstract class Plugin extends Component
{

    /**
     * include methods for translating texts
     */
    use HasTranslatorSupport;
    /**
     * include methods for working with features
     */
    use HasFeatureSupport;

    /**
     * Initializes the plugin, e.g. adds features or services using the addFeature(…) and addComponent(…) methods.
     *
     * Example implementations and explanations:
     *
     * <code>
     * protected function initPlugin(): void
     * {
     *   //  optionally add some parameters (may also be passed to PluginRegistrar::activate() as associative array)
     *   $this->addParameter('my-awesome-param', 'awesome-value');
     *   $this->addParameter('param', 'value');
     *
     *   //  # 1
     *
     *   //  add an awesome feature
     *   $this->addFeature(AwesomeFeature::class);
     *   //  add another awesome feature, but one a parameter is passed to (which will be grabbed from DI-container)
     *   $this->addFeature(AwesomeFeatureWithParam::class, 'my-awesome-param');
     *
     *   //  # 2
     *
     *   //  register an awesome service (component) to DI-container
     *   $this->addComponent(AwesomeService::class);
     *   //  register an awesome service with parameter
     *   $this->addComponent(AwesomeParameterizedService::class, 'param');
     *
     *   //  # 3
     *
     *   //  register a service that other components are passed to on instantiation
     *   $this->addComponent(AwesomeService::class, AnyComponent::class, OtherComponent::class);
     *
     *   //  NOTE:
     *   //  the parameter list of addComponent(…) is dependant on the parameter signature of the first class'
     *   //  constructor (so, here we assume that AwesomeService' 1st parameter is expected to be an instance of
     *   //  AnyComponent and the 2nd one is of type OtherComponent)
     *
     *   //  also ensure the passed components are added to the DI-container
     *   $this->addComponent(OtherComponent::class);
     *   $this->addComponent(AnyComponent::class);
     * }
     * </code>
     *
     * Please mention that the order of adding components (such as services) is unimportant since components will be
     * created and loaded at a later time.
     *
     * @see \Vierbeuter\WordPress\Plugin::addFeature()
     * @see \Vierbeuter\WordPress\Plugin::addComponent()
     * @see \Vierbeuter\WordPress\Plugin::addParameter()
     */
    abstract public function initPlugin(): void;
}
