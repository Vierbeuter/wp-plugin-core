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
}
