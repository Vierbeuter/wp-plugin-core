<?php

namespace Vierbeuter\WordPress\Service;

use Vierbeuter\WordPress\Feature\AdminPanel\AdminPage\WpOptionsPage;

/**
 * The WpmlWpOptions service provides methods for easily accessing <code>wp_options</code> whilst supporting WPML.
 *
 * @package Vierbeuter\WordPress\Service
 */
class WpmlWpOptions extends WpOptions
{

    /**
     * Returns the <code>wp_options</code> value for given option name. Result may have been translated using WPML.
     *
     * @param string $name the option's name
     * @param mixed|null $default default return value in case of no wp_option found for given option name
     * @param string|null $langCode (optional) language code to load the option value with, defaults to currently
     *     active language as given by WPML
     *
     * @return mixed
     *
     * @see \Vierbeuter\WordPress\Service\WpmlWpOptions::updateOption()
     */
    protected function getOption(string $name, $default = null, string $langCode = null)
    {
        return parent::getOption($this->getLanguageSpecificOptionName($name, $this->getLangCode($langCode)), $default);
    }

    /**
     * Saves given value to <code>wp_options</code> with given option name and registers a new string to be translated
     * using WPML.
     *
     * @param string $name the option's name
     * @param mixed $value the option's value
     * @param string|null $langCode (optional) language code to save the option value with, defaults to currently
     *     active language as given by WPML
     *
     * @return bool
     *
     * @see \Vierbeuter\WordPress\Service\WpmlWpOptions::getOption()
     */
    protected function updateOption(string $name, $value, string $langCode = null): bool
    {
        return parent::updateOption($this->getLanguageSpecificOptionName($name, $this->getLangCode($langCode)), $value);
    }

    /**
     * Returns the <code>wp_options</code> value for given key.
     *
     * Always use this method to load a value from the storage if it has been stored to <code>wp_options</code> using
     * the set(…) method.
     *
     * @param string $key the option's key
     * @param mixed|null $default default return value in case of no wp_option found for given key
     * @param string|null $langCode (optional) language code to save the option value with, defaults to currently
     *     active language as given by WPML
     *
     * @return mixed
     *
     * @see \Vierbeuter\WordPress\Service\WpOptions::set()
     */
    public function get(string $key, $default = null, string $langCode = null)
    {
        return $this->getOption($this->getDbMetaKey($key), $default, $langCode);
    }

    /**
     * Saves given value to <code>wp_options</code> with given key.
     *
     * When storing values with this method then always use the get(…) method to load these values from storage.
     *
     * @param string $key the option's key
     * @param mixed $value the option's value
     * @param string|null $langCode (optional) language code to save the option value with, defaults to currently
     *     active language as given by WPML
     *
     * @return bool
     *
     * @see \Vierbeuter\WordPress\Service\WpOptions::get()
     */
    public function set(string $key, $value, string $langCode = null): bool
    {
        return $this->updateOption($this->getDbMetaKey($key), $value, $langCode);
    }

    /**
     * Returns the <code>wp_options</code> value for given WpOptionsPage and field slug.
     *
     * Use this method to load a value from <code>wp_options</code> if it has been stored using the setByPage(…) method.
     *
     * @param \Vierbeuter\WordPress\Feature\AdminPanel\AdminPage\WpOptionsPage $wpOptionsPage
     * @param string $fieldSlug the field's slug as defined in given page class
     * @param mixed|null $default default return value in case of no wp_option found for given field slug
     * @param string|null $langCode (optional) language code to save the option value with, defaults to currently
     *     active language as given by WPML
     *
     * @return mixed
     *
     * @see \Vierbeuter\WordPress\Service\WpOptions::setByPage()
     */
    public function getByPage(WpOptionsPage $wpOptionsPage, string $fieldSlug, $default = null, string $langCode = null)
    {
        return $this->getOption($this->getDbMetaKey($fieldSlug, $wpOptionsPage), $default, $langCode);
    }

    /**
     * Saves given value to <code>wp_options</code> for given WpOptionsPage and field slug.
     *
     * When storing values with this method then always use one of the methods getByPage(…) or getByPageClass(…) to
     * load these values from storage.
     *
     * @param \Vierbeuter\WordPress\Feature\AdminPanel\AdminPage\WpOptionsPage $wpOptionsPage
     * @param string $fieldSlug the field's slug as defined in given page class
     * @param mixed $value the option's value
     * @param string|null $langCode (optional) language code to save the option value with, defaults to currently
     *     active language as given by WPML
     *
     * @return bool
     *
     * @see \Vierbeuter\WordPress\Service\WpOptions::getByPage()
     * @see \Vierbeuter\WordPress\Service\WpOptions::getByPageClass()
     */
    public function setByPage(WpOptionsPage $wpOptionsPage, string $fieldSlug, $value, string $langCode = null): bool
    {
        return $this->updateOption($this->getDbMetaKey($fieldSlug, $wpOptionsPage), $value, $langCode);
    }

    /**
     * Returns the <code>wp_options</code> value for given classname (that class has to be a sub-class of
     * WpOptionsPage)
     * and field slug.
     *
     * Use this method to load a value from <code>wp_options</code> if it has been stored using the setByPage(…)
     * method &rarr; which it is if the WP option is defined by a field in given page class.
     *
     * @param string $wpOptionsPageClass the page's class name where the wp_options field is defined, the class has to
     *     be a sub-class of WpOptionsPage
     * @param string $fieldSlug the field's slug as defined in given page class
     * @param mixed|null $default default return value in case of no wp_option found for given field slug
     * @param string|null $langCode (optional) language code to save the option value with, defaults to currently
     *     active language as given by WPML
     *
     * @return mixed
     *
     * @see \Vierbeuter\WordPress\Service\WpOptions::setByPage()
     * @see \Vierbeuter\WordPress\Feature\AdminPanel\AdminPage\WpOptionsPage
     */
    public function getByPageClass(string $wpOptionsPageClass, string $fieldSlug, $default = null, string $langCode = null)
    {
        //  check given class first
        if (empty($wpOptionsPageClass) || !is_subclass_of($wpOptionsPageClass, WpOptionsPage::class)) {
            throw new \InvalidArgumentException('Given class "' . $wpOptionsPageClass . '" needs to be a valid sub-class of "' . WpOptionsPage::class . '"');
        }

        /** @var \Vierbeuter\WordPress\Feature\AdminPanel\AdminPage\WpOptionsPage $wpOptionsPage */
        $wpOptionsPage = $this->getComponent($wpOptionsPageClass);

        return $this->getByPage($wpOptionsPage, $fieldSlug, $default, $langCode);
    }

    /**
     * Returns the localized option name for given option and language code.
     *
     * @param string $name the option's name
     * @param string $langCode the language code
     *
     * @return string
     */
    protected function getLanguageSpecificOptionName(string $name, string $langCode): string
    {
        return $name . '_' . $langCode;
    }

    /**
     * Returns either the given language code or the currently active one as given by WPML.
     *
     * @param string|null $langCode (optional) language code to load or save an option value with
     *
     * @return string
     */
    protected function getLangCode(string $langCode = null): string
    {
        $langCode = empty($langCode) ? $this->getCurrentLanguageCode() : $langCode;

        return $langCode;
    }

    /**
     * Determines the currently active language code as given by WPML.
     *
     * @return string
     *
     * @throws \Exception if WPML is not active or loaded (yet)
     */
    protected function getCurrentLanguageCode(): string
    {
        global $sitepress;

        if (empty($sitepress)) {
            throw new \LogicException('WPML is either not installed/active or currently not loaded yet.');
        }

        return $sitepress->get_current_language();
    }
}
