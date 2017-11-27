<?php

namespace Vierbeuter\WordPress\Feature\AdminPanel\AdminPage;

use Vierbeuter\WordPress\PluginData;

/**
 * WpOptionsPage is the base class for implementing and adding settings pages to the wp-admin panel that entries of
 * <code>wp_options</code> can be configured with. For ease of use you just have to provide a list of custom fields
 * (each one will automatically be mapped to an entry of <code>wp_options</code>).
 *
 * @package Vierbeuter\WordPress\Feature\AdminPanel\AdminPage
 */
abstract class WpOptionsPage extends AdminPage
{

    /**
     * @var \Vierbeuter\WordPress\PluginData
     */
    private $pluginData;

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
            $fieldId = $this->getDbMetaKey($field->getSlug());

            if (isset($post[$fieldId])) {
                update_option($fieldId, $post[$fieldId]);
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
        foreach ($this->getFields() as $field) {
            $fieldId = $this->getDbMetaKey($field->getSlug());
            $value = get_option($fieldId, null);

            $field->renderConfig($fieldId, $value);
        }

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
     * Returns the database key for accessing the wp_options-value.
     *
     * @param string $fieldSlug
     *
     * @return string
     */
    protected function getDbMetaKey(string $fieldSlug): string
    {
        //  concat plugin name and slug of page with custom-field slug
        return $this->pluginData->getPluginName() . '-' . $this->getSlug() . '-' . $fieldSlug;
    }
}
