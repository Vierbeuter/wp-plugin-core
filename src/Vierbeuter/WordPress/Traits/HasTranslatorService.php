<?php

namespace Vierbeuter\WordPress\Traits;

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
            $this->addComponent('translator', new Translator($this->getPluginName()));
        }
    }

    /**
     * Returns the translator service.
     *
     * @return null|\Vierbeuter\WordPress\Service\Translator
     */
    private function getTranslator(): ?Translator
    {
        return $this->getComponent('translator');
    }

    /**
     * Translates the given text.
     *
     * @param string $text
     *
     * @return string
     */
    public function translate(string $text): string
    {
        return $this->getTranslator()->translate($text);
    }
}
