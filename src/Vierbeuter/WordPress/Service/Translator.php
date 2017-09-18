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
     * @var string
     */
    protected $languagesDir;

    /**
     * Translator constructor.
     *
     * @param string $domain text domain (name of the plugin)
     * @param string $languagesDir absolute directory path to language files containing all translations
     */
    public function __construct(string $domain, string $languagesDir)
    {
        //  set domain
        $this->domain = $domain;
        //  set directory path, ensure to have an absolute path with leading and trailing slash
        $this->languagesDir = '/' . trim($languagesDir, '/') . '/';

        //  load translation file
        $this->loadTextDomain();
    }

    /**
     * Returns the translated text for the given one.
     *
     * @param string $text
     *
     * @return string
     *
     * @see __()
     */
    public function translate(string $text): string
    {
        return __($text, $this->domain);
    }

    /**
     * Loads the translation file for current locale.
     */
    protected function loadTextDomain(): void
    {
        /**
         * Filters a plugin's locale.
         *
         * @since 3.0.0
         *
         * @param string $locale The plugin's current locale.
         * @param string $domain Text domain. Unique identifier for retrieving translated strings.
         */
        $locale = apply_filters('plugin_locale', is_admin() ? get_user_locale() : get_locale(), $this->domain);

        //  build path to *.mo file as also done in several functions located in …/wp-includes/l10n.php
        /** @see load_plugin_textdomain() */
        $moFile = $this->domain . '-' . $locale . '.mo';

        //  load the file and "link" with domain
        load_textdomain($this->domain, $this->languagesDir . $moFile);
    }
}
