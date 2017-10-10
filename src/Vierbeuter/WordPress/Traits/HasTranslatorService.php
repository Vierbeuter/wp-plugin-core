<?php

namespace Vierbeuter\WordPress\Traits;

use Vierbeuter\WordPress\Service\CoreTranslator;
use Vierbeuter\WordPress\Service\PluginTranslator;
use Vierbeuter\WordPress\Service\Translator;

/**
 * The HasTranslatorService trait adds methods for translating texts.
 *
 * It's required to be used in combination with HasDependencyInjectionContainer trait.
 *
 * @package Vierbeuter\WordPress\Traits
 *
 * @see \Vierbeuter\WordPress\Traits\HasDependencyInjectionContainer
 */
trait HasTranslatorService
{

    /**
     * Adds the translator service to the DI-container (only once).
     */
    private function addTranslator(): void
    {
        //  add service only once
        if (empty($this->getTranslator())) {
            //  translator for the actual plugin
            $translator = new PluginTranslator($this->getPluginName(), $this->getPluginDir() . 'languages/');
            $this->addComponent('translator', $translator);
            //  translator for the base classes / plugin core
            $coreTranslator = new CoreTranslator();
            $this->addComponent('translator_core', $coreTranslator);
        }
    }

    /**
     * Returns the translator service.
     *
     * @param bool $returnCoreTranslator determines if the translator component for the plugin core has to be returned;
     *     defaults to FALSE to return the translator component of the actual plugin
     *
     * @return null|\Vierbeuter\WordPress\Service\Translator
     */
    private function getTranslator(bool $returnCoreTranslator = false): ?Translator
    {
        $name = 'translator' . ($returnCoreTranslator ? '_core' : '');

        return $this->getComponent($name);
    }

    /**
     * Translates the given text.
     *
     * @param string $text
     * @param bool $useCoreTranslator determines if the translator component for the plugin core has to be used;
     *     defaults to FALSE to use the translator component of the actual plugin
     *
     * @return string
     */
    public function translate(string $text, bool $useCoreTranslator = false): string
    {
        return $this->getTranslator($useCoreTranslator)->translate($text);
    }
}
