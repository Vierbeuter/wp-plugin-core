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
     * @param \Vierbeuter\WordPress\Feature\Feature $feature
     *
     * @throws \Exception
     */
    protected function addFeature(Feature $feature): void
    {
        //  add feature only once
        if (empty($this->getFeature(get_class($feature)))) {
            //  add translator to feature
            $feature->setTranslator($this->getTranslator());

            //  add to DI-container
            $this->addComponent(get_class($feature), $feature);

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
