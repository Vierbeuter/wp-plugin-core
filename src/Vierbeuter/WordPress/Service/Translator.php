<?php

namespace Vierbeuter\WordPress\Service;

/**
 * The Translator service provides methods for translating texts.
 *
 * @package Vierbeuter\WordPress\Service
 */
class Translator extends Service
{

    /**
     * @var string
     */
    protected $domain;

    /**
     * Translator constructor.
     *
     * @param string $domain
     */
    public function __construct(string $domain)
    {
        $this->domain = $domain;
    }

    /**
     * Returns the translated text for the given one.
     *
     * Optionally a context can be passed as information for the translators in case of the given text collides with
     * similar translatable text found at another location but with different translated context.
     *
     * @param string $text
     * @param string|null $context
     *
     * @return string
     *
     * @see __()
     * @see _x()
     */
    public function translate(string $text, string $context = null): string
    {
        if (empty($context)) {
            return __($text, $this->domain);
        }

        return _x($text, $context, $this->domain);
    }
}
