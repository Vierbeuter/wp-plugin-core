<?php

namespace Vierbeuter\WordPress\Traits;

use Vierbeuter\WordPress\Feature\Feature;

/**
 * The HasFeatureSupport trait provides methods for working with features.
 *
 * It's required to be used in combination with HasDependencyInjectionContainer trait.
 *
 * @package Vierbeuter\WordPress\Traits
 *
 * @see \Vierbeuter\WordPress\Traits\HasDependencyInjectionContainer
 */
trait HasFeatureSupport
{

    /**
     * Adds the given feature to the plugin and enables it.
     *
     * @param string $featureClass the feature's class name of the feature, has to be a sub-class of Feature
     * @param array $paramNames names of parameters to be passed to the feature's constructor, the parameters are
     *     expected to be found in the DI-containter as well, ensure they are added before accessing the given feature
     *
     * @throws \Exception
     *
     * @see \Vierbeuter\WordPress\Feature\Feature
     */
    protected function addFeature(string $featureClass, ...$paramNames): void
    {
        //  check feature class first
        if (empty($featureClass) || !is_subclass_of($featureClass, Feature::class)) {
            throw new \InvalidArgumentException('Given class "' . $featureClass . '" needs to be a valid sub-class of "' . Feature::class . '"');
        }

        //  add feature only once
        if (empty($this->getFeature($featureClass))) {
            //  add to DI-container
            $this->addComponent($featureClass, ...$paramNames);
            //  instantiate by getting the feature from container
            /** @var \Vierbeuter\WordPress\Feature\Feature $feature */
            $feature = $this->getComponent($featureClass);

            //  activate feature
            $feature->activate();
        }
    }

    /**
     * Returns the (previously added) feature for given classname.
     *
     * @param string $classname
     *
     * @return null|\Vierbeuter\WordPress\Feature\Feature
     */
    protected function getFeature(string $classname): ?Feature
    {
        return $this->getComponent($classname);
    }
}
