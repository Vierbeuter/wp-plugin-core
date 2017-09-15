<?php

namespace Vierbeuter\WordPress\Feature\Traits;

use Vierbeuter\WordPress\Service\Translator;

/**
 * The HasTranslator trait provides methods for translating texts.
 *
 * @package Vierbeuter\WordPress\Feature\Traits
 */
trait HasTranslator
{

    /**
     * @var \Vierbeuter\WordPress\Service\Translator
     */
    private $translator;

    /**
     * @var \Vierbeuter\WordPress\Service\Translator
     */
    private $vbTranslator;

    /**
     * Returns the translator.
     *
     * @return \Vierbeuter\WordPress\Service\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * Sets the translator.
     *
     * @param \Vierbeuter\WordPress\Service\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * Translates the given text, optionally using the context string passed as second parameter.
     *
     * @param string $text
     * @param string|null $context
     *
     * @return string
     */
    public function translate(string $text, string $context = null): string
    {
        return $this->translator->translate($text, $context);
    }

    /**
     * Returns the vbTranslator for translating texts of plugin core.
     *
     * @return \Vierbeuter\WordPress\Service\Translator
     */
    public function getVbTranslator(): Translator
    {
        return $this->vbTranslator;
    }

    /**
     * Sets the vbTranslator for translating texts of plugin core.
     *
     * @param \Vierbeuter\WordPress\Service\Translator $vbTranslator
     */
    public function setVbTranslator(Translator $vbTranslator)
    {
        $this->vbTranslator = $vbTranslator;
    }

    /**
     * Translates the given text, optionally using the context string passed as second parameter.
     *
     * To be used within core components only (unless you want to get untranslated texts as return value).
     *
     * @param string $text
     * @param string|null $context
     *
     * @return string
     */
    public function vbTranslate(string $text, string $context = null): string
    {
        return $this->vbTranslator->translate($text, $context);
    }
}
