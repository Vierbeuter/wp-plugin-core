<?php

namespace Vierbeuter\WordPress\Traits;

use Vierbeuter\WordPress\Service\CoreTranslator;
use Vierbeuter\WordPress\Service\PluginTranslator;
use Vierbeuter\WordPress\Service\Translator;

/**
 * The HasTranslatorSupport trait adds methods for translating texts.
 *
 * @package Vierbeuter\WordPress\Traits
 */
trait HasTranslatorSupport
{

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
        $name = $returnCoreTranslator ? CoreTranslator::class : PluginTranslator::class;

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
