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
     * @param string $text
     *
     * @return string
     */
    public function translate(string $text): string
    {
        return __($text, $this->domain);
    }
}
