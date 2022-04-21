<?php

namespace Vierbeuter\WordPress\Feature\CustomField;

/**
 * WpEditor (WYSIWYG editor for formatted text) to be used for a post-type's custom-field.
 *
 * @package Vierbeuter\WordPress\Feature\CustomField
 */
class WpEditor extends CustomField
{

    /**
     * settings array passed to wp_editor(…) function
     *
     * @var array
     *
     * @see https://developer.wordpress.org/reference/functions/wp_editor/#parameters
     * @see https://developer.wordpress.org/reference/classes/_wp_editors/parse_settings/#parameters
     */
    protected $settings;

    /**
     * WpEditor constructor.
     *
     * @param string $slug
     * @param string $label
     * @param null $description
     * @param array $settings
     *
     * @see \Vierbeuter\WordPress\Feature\CustomField\WpEditor::getDefaultSettings()
     */
    public function __construct($slug, $label, $description = null, array $settings = [])
    {
        parent::__construct($slug, $label, $description);

        $this->settings = array_merge($this->getDefaultSettings(), $settings);
    }

    /**
     * Returns the default settings for the wp_editor.
     *
     * To keep things as simple as possible the returned settings array makes the toolbar offering only the most
     * important buttons (bold, italic, link and lists). Pass <code>['tinymce'=>true]</code> as settings array to the
     * constructor to override this simplicity and to restore WP's WYSIWYG editor with all its standard buttons and
     * elements.
     *
     * @return array
     */
    protected function getDefaultSettings(): array
    {
        return [
            'media_buttons' => false,
            'quicktags' => false,
            'tinymce' => [
                'height' => 200,
                'inline_styles' => false,
                'paste_as_text' => true,
                'entity_encoding' => 'raw',
                'toolbar1' => 'bold italic | link | bullist numlist',
            ],
        ];
    }

    /**
     * Renders the input's markup.
     *
     * @param string $fieldId
     * @param string|null $value
     * @param \WP_Post|\WP_Term|null $postOrTerm
     */
    protected function renderField(string $fieldId, string $value = null, $postOrTerm = null): void
    {
        wp_editor($value, $fieldId, $this->settings);
    }

    /**
     * Determines if the custom-field value has to be sanitized on save using the sanitize_text_field() function.
     * This is useful to filter line breaks, HTML markup and such stuff from the field value.
     *
     * Will be called on save() method of FieldGroup class.
     *
     * @return bool
     *
     * @see sanitize_text_field()
     * @see \Vierbeuter\WordPress\Feature\CustomField\FieldGroup::save()
     */
    public function sanitizeValueOnSave(): bool
    {
        return false;
    }

    /**
     * Hooks into `admin_enqueue_scripts` and loads additional JS and CSS ressources for WP-admin panel.
     *
     * Use WordPress functions `wp_enqueue_script(…)` and `wp_enqueue_style(…)` to add assets.
     * As needed don't hesitate to check current page with `get_current_screen()` function for adding several scripts
     * only on specific pages of WP's admin panel.
     *
     * @see https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
     * @see https://developer.wordpress.org/reference/functions/wp_enqueue_script/
     * @see https://developer.wordpress.org/reference/functions/wp_enqueue_style/
     * @see https://developer.wordpress.org/reference/functions/get_current_screen/
     */
    public function enqueueScripts(): void
    {
        // load scripts and such stuff for rendering the editor
        wp_enqueue_editor();
    }
}
