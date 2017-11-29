<?php

namespace Vierbeuter\WordPress\Service;

use Vierbeuter\WordPress\Feature\AdminPanel\AdminPage\WpOptionsPage;
use Vierbeuter\WordPress\PluginData;

/**
 * The WpOptions service provides methods for easily accessing <code>wp_options</code>.
 *
 * @package Vierbeuter\WordPress\Service
 */
class WpOptions extends Service
{

    /**
     * @var \Vierbeuter\WordPress\PluginData
     */
    protected $pluginData;

    /**
     * WpOptionsPage constructor.
     *
     * @param \Vierbeuter\WordPress\PluginData $pluginData
     */
    public function __construct(PluginData $pluginData)
    {
        $this->pluginData = $pluginData;
    }

    /**
     * Returns the database key to access the wp_options-value for given field slug and optional wp_options page.
     *
     * @param string $fieldSlug
     * @param \Vierbeuter\WordPress\Feature\AdminPanel\AdminPage\WpOptionsPage $wpOptionsPage
     *
     * @return string
     */
    protected function getDbMetaKey(string $fieldSlug, WpOptionsPage $wpOptionsPage = null): string
    {
        //  determine all parts of the wp_option's key
        $pluginPart = $this->pluginData->getPluginName();
        $pagePart = empty($wpOptionsPage) ? '' : ('-' . $wpOptionsPage->getSlug());
        $fieldPart = '-' . $fieldSlug;

        //  concat plugin name and slug of page with custom-field slug to avoid any kind of naming collision with other
        //  plugins storing wp_options to the database
        return $pluginPart . $pagePart . $fieldPart;
    }

    /**
     * Returns the <code>wp_options</code> value for given key.
     *
     * Always use this method to load a value from the storage if it has been stored to <code>wp_options</code> using
     * the set(…) method.
     *
     * @param string $key the option's key
     * @param mixed|null $default default return value in case of no wp_option found for given key
     *
     * @return mixed
     *
     * @see \Vierbeuter\WordPress\Service\WpOptions::set()
     */
    public function get(string $key, $default = null)
    {
        return get_option($this->getDbMetaKey($key), $default);
    }

    /**
     * Saves given value to <code>wp_options</code> with given key.
     *
     * When storing values with this method then always use the get(…) method to load these values from storage.
     *
     * @param string $key the option's key
     * @param mixed $value the option's value
     *
     * @return bool
     *
     * @see \Vierbeuter\WordPress\Service\WpOptions::get()
     */
    public function set(string $key, $value): bool
    {
        return update_option($this->getDbMetaKey($key), $value);
    }

    /**
     * Returns the <code>wp_options</code> value for given WpOptionsPage and field slug.
     *
     * Use this method to load a value from <code>wp_options</code> if it has been stored using the setByPage(…) method.
     *
     * @param \Vierbeuter\WordPress\Feature\AdminPanel\AdminPage\WpOptionsPage $wpOptionsPage
     * @param string $fieldSlug the field's slug as defined in given page class
     * @param mixed|null $default default return value in case of no wp_option found for given field slug
     *
     * @return mixed
     *
     * @see \Vierbeuter\WordPress\Service\WpOptions::setByPage()
     */
    public function getByPage(WpOptionsPage $wpOptionsPage, string $fieldSlug, $default = null)
    {
        return get_option($this->getDbMetaKey($fieldSlug, $wpOptionsPage), $default);
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
     *
     * @return bool
     *
     * @see \Vierbeuter\WordPress\Service\WpOptions::getByPage()
     * @see \Vierbeuter\WordPress\Service\WpOptions::getByPageClass()
     */
    public function setByPage(WpOptionsPage $wpOptionsPage, string $fieldSlug, $value): bool
    {
        return update_option($this->getDbMetaKey($fieldSlug, $wpOptionsPage), $value);
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
     *
     * @return mixed
     *
     * @see \Vierbeuter\WordPress\Service\WpOptions::setByPage()
     * @see \Vierbeuter\WordPress\Feature\AdminPanel\AdminPage\WpOptionsPage
     */
    public function getByPageClass(string $wpOptionsPageClass, string $fieldSlug, $default = null)
    {
        //  check given class first
        if (empty($featureClass) || !is_subclass_of($featureClass, WpOptionsPage::class)) {
            throw new \InvalidArgumentException('Given class "' . $wpOptionsPageClass . '" needs to be a valid sub-class of "' . WpOptionsPage::class . '"');
        }

        /** @var \Vierbeuter\WordPress\Feature\AdminPanel\AdminPage\WpOptionsPage $wpOptionsPage */
        $wpOptionsPage = $this->getComponent($wpOptionsPageClass);

        return $this->getByPage($wpOptionsPage, $fieldSlug, $default);
    }
}
