<?php

namespace Vierbeuter\WordPress\Feature\AdminPanel\AdminPage;

use Vierbeuter\WordPress\Service\WpOptions;

/**
 * WpOptionsPage is the base class for implementing and adding settings pages to the wp-admin panel that entries of
 * <code>wp_options</code> can be configured with. For ease of use you just have to provide a list of custom fields
 * (each one will automatically be mapped to an entry of <code>wp_options</code>).
 *
 * To read and update your <code>wp_options</code> entries that are defined in this page through these custom-fields
 * just use the WpOptions service.
 *
 * @package Vierbeuter\WordPress\Feature\AdminPanel\AdminPage
 *
 * @see \Vierbeuter\WordPress\Service\WpOptions
 */
abstract class WpOptionsPage extends AdminPage
{

    /**
     * @var \Vierbeuter\WordPress\Service\WpOptions
     */
    protected $wpOptions;

    /**
     * WpOptionsPage constructor.
     *
     * @param \Vierbeuter\WordPress\Service\WpOptions $wpOptions
     */
    public function __construct(WpOptions $wpOptions)
    {
        $this->wpOptions = $wpOptions;
    }

    /**
     * Intializes the WordPress hooks as defined in the sub-class and in the fields of this wp_options page.
     *
     * Override getActionHooks() and getFilterHooks() methods to define these hooks.
     *
     * @see \Vierbeuter\WordPress\Feature\Traits\HasWpHookSupport::getActionHooks()
     * @see \Vierbeuter\WordPress\Feature\Traits\HasWpHookSupport::getFilterHooks()
     */
    public function initWpHooks(): void
    {
        parent::initWpHooks();

        //  let all fields register their WP-hook implementations
        foreach ($this->getFields() as $field) {
            $field->initWpHooks();
        }
    }

    /**
     * Returns all custom fields.
     *
     * @return \Vierbeuter\WordPress\Feature\CustomField\CustomField[]
     */
    abstract public function getFields(): array;

    /**
     * Handles post requests.
     *
     * @param array $post
     */
    protected function handlePost(array $post): void
    {
        //  update wp_options for all custom fields
        foreach ($this->getFields() as $field) {
            $fieldId = $this->getFieldId($field->getSlug());

            if (isset($post[$fieldId])) {
                //  POST values are escaped by default using addslashes(), see wp_magic_quotes() and add_magic_quotes()
                /** @see wp_magic_quotes() */
                /** @see add_magic_quotes() */
                //  --> unescape the value with stripslashes() which is the counterpart of addslashes()
                $post[$fieldId] = stripslashes($post[$fieldId]);

                $this->wpOptions->setByPage($this, $field->getSlug(), $post[$fieldId]);
            }
        }
    }

    /**
     * Renders the page.
     */
    protected function render(): void
    {
        //  wrapper begin
        echo '<div class="wrap">';

        //  page title
        echo '<h2>' . $this->getPageTitle() . '</h2>';

        //  table and form header
        echo '<div class="postbox">';
        echo '<div class="inside">';
        echo '<form method="post" enctype="multipart/form-data">';
        echo '<table class="form-table">';

        //  render all custom fields
        $this->renderFields();

        //  update button
        echo '<tr>';
        echo '<th scope="row"></th>';
        echo '<td>';
        echo '<input type="submit" name="save-tiles-config" value="' . $this->translate('Save') . '" class="button button-primary button-large" />';
        echo '</td>';
        echo '</tr>';

        //  table and form footer
        echo '</table>';
        echo '</form>';
        echo '</div>';
        echo '</div>';

        //  wrapper end
        echo '</div>';
    }

    /**
     * Returns the field id for usage in rendered form and POST object.
     *
     * @param string $fieldSlug
     *
     * @return string
     */
    protected function getFieldId(string $fieldSlug): string
    {
        //  concat slug of page with custom-field slug
        return $this->getSlug() . '-' . $fieldSlug;
    }

    /**
     * Renders all custom fields.
     */
    protected function renderFields(): void
    {
        foreach ($this->getFields() as $field) {
            $fieldId = $this->getFieldId($field->getSlug());
            $value = $this->wpOptions->getByPage($this, $field->getSlug());

            $field->renderConfig($fieldId, $value);
        }
    }
}
