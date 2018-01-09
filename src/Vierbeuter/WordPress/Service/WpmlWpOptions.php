<?php

namespace Vierbeuter\WordPress\Service;

/**
 * The WpmlWpOptions service provides methods for easily accessing <code>wp_options</code> whilst supporting WPML.
 *
 * @package Vierbeuter\WordPress\Service
 *
 * @see https://wpml.org/forums/topic/translating-plugin-strings-stored-in-wp_options/#post-674903
 * @see https://wpml.org/documentation/support/wpml-coding-api/wpml-hooks-reference/
 */
class WpmlWpOptions extends WpOptions
{

    /**
     * Returns the <code>wp_options</code> value for given option name. Result may have been translated using WPML.
     *
     * @param string $name the option's name
     * @param mixed|null $default default return value in case of no wp_option found for given option name
     *
     * @return mixed
     *
     * @see \Vierbeuter\WordPress\Service\WpmlWpOptions::updateOption()
     */
    protected function getOption(string $name, $default = null)
    {
        //  get original value
        $value = parent::getOption($name, $default);

        //  get translated value
        /** @see https://wpml.org/wpml-hook/wpml_translate_single_string/ */
        $value = apply_filters('wpml_translate_single_string', $value, $this->pluginData->getPluginSlug(), $name);

        return $value;
    }

    /**
     * Saves given value to <code>wp_options</code> with given option name and registers a new string to be translated
     * using WPML.
     *
     * @param string $name the option's name
     * @param mixed $value the option's value
     *
     * @return bool
     *
     * @see \Vierbeuter\WordPress\Service\WpmlWpOptions::getOption()
     */
    protected function updateOption(string $name, $value): bool
    {
        //  save original value
        $uccess = parent::updateOption($name, $value);

        //  add value to translatables
        /** @see https://wpml.org/wpml-hook/wpml_register_single_string/ */
        do_action('wpml_register_single_string', $this->pluginData->getPluginSlug(), $name, $value);

        return $uccess;
    }
}
