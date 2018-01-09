<?php

namespace Vierbeuter\WordPress\Feature\AdminPanel\AdminPage;

use Vierbeuter\WordPress\Service\WpmlWpOptions;

/**
 * WpmlWpOptionsPage is the base class for implementing and adding settings pages to the wp-admin panel that entries of
 * <code>wp_options</code> can be configured with. For ease of use you just have to provide a list of custom fields
 * (each one will automatically be mapped to an entry of <code>wp_options</code>).
 *
 * These options pages are translatable using WPML.
 *
 * To read and update your <code>wp_options</code> entries that are defined in this page through these custom-fields
 * just use the WpmlWpOptions service.
 *
 * @package Vierbeuter\WordPress\Feature\AdminPanel\AdminPage
 *
 * @see \Vierbeuter\WordPress\Service\WpmlWpOptions
 */
abstract class WpmlWpOptionsPage extends WpOptionsPage
{

    /**
     * WpmlWpOptionsPage constructor.
     *
     * @param \Vierbeuter\WordPress\Service\WpmlWpOptions $wpOptions
     */
    public function __construct(WpmlWpOptions $wpOptions)
    {
        //  override parent constructor to enforce the usage of WpmlWpOptions instead of WpOptions
        parent::__construct($wpOptions);
    }

    /**
     * Handles post requests for current or all languages.
     *
     * @param array $post
     *
     * @throws \Exception if WPML is not active or loaded (yet)
     */
    protected function handlePost(array $post): void
    {
        global $sitepress;

        if (empty($sitepress)) {
            throw new \LogicException('WPML is either not installed/active or currently not loaded yet.');
        }

        //  if specific language selected
        if ($sitepress->get_current_language() != 'all') {
            parent::handlePost($post);

            return;
        }

        /** @var WpmlWpOptions $wpOptions */
        $wpOptions = $this->wpOptions;

        //  update wp_options for all custom fields
        foreach ($this->getFields() as $field) {
            foreach ($sitepress->get_active_languages() as $language) {
                $langCode = $language['code'];
                $fieldId = $this->getFieldId($field->getSlug()) . '_' . $langCode;

                if (isset($post[$fieldId])) {
                    //  POST values are escaped by default using addslashes(), see wp_magic_quotes() and add_magic_quotes()
                    /** @see wp_magic_quotes() */
                    /** @see add_magic_quotes() */
                    //  --> unescape the value with stripslashes() which is the counterpart of addslashes()
                    $post[$fieldId] = stripslashes($post[$fieldId]);

                    $wpOptions->setByPage($this, $field->getSlug(), $post[$fieldId], $langCode);
                }
            }
        }
    }

    /**
     * Renders all custom fields for current or all languages.
     *
     * @throws \Exception if WPML is not active or loaded (yet)
     */
    protected function renderFields(): void
    {
        global $sitepress;

        if (empty($sitepress)) {
            throw new \LogicException('WPML is either not installed/active or currently not loaded yet.');
        }

        //  if specific language selected
        if ($sitepress->get_current_language() != 'all') {
            parent::renderFields();

            return;
        }

        /** @var WpmlWpOptions $wpOptions */
        $wpOptions = $this->wpOptions;

        foreach ($this->getFields() as $field) {
            foreach ($sitepress->get_active_languages() as $language) {
                $langCode = $language['code'];
                $fieldId = $this->getFieldId($field->getSlug()) . '_' . $langCode;
                $value = $wpOptions->getByPage($this, $field->getSlug(), null, $langCode);

                $field->renderWpmlConfig($fieldId, $langCode, $value);
            }
        }
    }
}
