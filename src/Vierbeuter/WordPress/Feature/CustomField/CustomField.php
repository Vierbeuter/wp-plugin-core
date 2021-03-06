<?php

namespace Vierbeuter\WordPress\Feature\CustomField;

use Vierbeuter\WordPress\Feature\Traits\HasWpHookSupport;

/**
 * Custom-field for a custom post-type.
 *
 * @package Vierbeuter\WordPress\Feature\CustomField
 *
 * @see \Vierbeuter\WordPress\Feature\CustomPostType\CustomPostType
 */
abstract class CustomField
{

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string|null
     */
    protected $description;

    /**
     * include methods for being able to provide WP-hook implementations
     */
    use HasWpHookSupport;

    /**
     * CustomField constructor.
     *
     * @param string $slug
     * @param string $label
     * @param string|null $description
     */
    function __construct(string $slug, string $label, string $description = null)
    {
        $this->slug = $slug;
        $this->label = $label;
        $this->description = $description;
    }

    /**
     * Translates the given text.
     *
     * @param string $text
     *
     * @return string
     */
    public function translate(string $text): string
    {
        //  TODO: use translator!
        return $text;
    }

    /**
     * Translates the given text using the vbTranslator.
     *
     * To be used within core components only (unless you want to get untranslated texts as return value).
     *
     * @param string $text
     *
     * @return string
     */
    public function vbTranslate(string $text): string
    {
        //  TODO: use translator!
        return __($text, 'vb-wp-plugin-core');
    }

    /**
     * Sets the slug.
     *
     * Caution: This is not a "fully qualified" slug of the custom field, but the last part of it which is just the
     * $slug as given to constructor.
     *
     * To determine the "fully qualified" slug please use getFieldDbMetaKey() method of class FieldGroup instead.
     *
     * @param string $slug
     *
     * @see \Vierbeuter\WordPress\Feature\CustomField\CustomField::__construct()
     * @see \Vierbeuter\WordPress\Feature\CustomField\FieldGroup::getFieldDbMetaKey()
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * Returns the slug.
     *
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Sets the label.
     *
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * Returns the label.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Sets the description.
     *
     * @param string|null $description
     */
    public function setDescription(string $description = null): void
    {
        $this->description = $description;
    }

    /**
     * Returns the description.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Renders the markup of this custom-field.
     *
     * @param \WP_Post $post
     * @param string $fieldId
     * @param string|null $value
     */
    public function render(\WP_Post $post, string $fieldId, string $value = null): void
    {
        //  header area ("left side")
        echo '<th scope="row">';
        //  label
        $this->renderLabel($fieldId);
        echo '</th>';

        //  input area ("right side")
        echo '<td>';
        //  the actual input field
        $this->renderField($post, $fieldId, $value);
        //  optional description/usage hint
        /**
         * styling with CSS class "custom-field-note"
         *
         * @see \Vierbeuter\WordPress\Feature\AddCustomPostTypes::admin_head()
         */
        $this->renderDescription($fieldId, 'custom-field-note clear');
        //  other… e.g. Javascript
        $this->renderAnythingAfterField($post, $fieldId, $value);
        echo '</td>';
    }

    /**
     * Renders the markup of this custom-field for a taxonomy's term creation form.
     *
     * @param string $fieldId
     */
    public function renderTaxonomyNew(string $fieldId): void
    {
        echo '<div class="form-field term-' . $fieldId . '-wrap">';

        //  label
        $this->renderLabel($fieldId);

        //  the actual input field
        $this->renderField(null, $fieldId);
        //  optional description/usage hint
        $this->renderDescription($fieldId);
        //  other… e.g. Javascript
        $this->renderAnythingAfterField(null, $fieldId);

        echo '</div>';
    }

    /**
     * Renders the markup of this custom-field for a taxonomy's edit page.
     *
     * @param \WP_Term $term
     * @param string $fieldId
     * @param string|null $value
     */
    public function renderTaxonomyEdit(\WP_Term $term, string $fieldId, string $value = null): void
    {
        //  wrapper begin
        echo '<tr class="form-field term-' . $fieldId . '-wrap">';

        //  header area ("left side")
        echo '<th scope="row">';
        //  label
        $this->renderLabel($fieldId);
        echo '</th>';

        //  input area ("right side")
        echo '<td>';
        //  the actual input field
        $this->renderField($term, $fieldId, $value);
        //  optional description/usage hint
        $this->renderDescription($fieldId, 'description');
        //  other… e.g. Javascript
        $this->renderAnythingAfterField($term, $fieldId, $value);
        echo '</td>';

        //  wrapper end
        echo '</tr>';
    }

    /**
     * Renders the markup of this custom-field for a ConfigPage.
     *
     * @param string $fieldId
     * @param string|null $value
     *
     * @see \Vierbeuter\WordPress\Feature\AdminPanel\AdminPage\WpOptionsPage
     */
    public function renderConfig(string $fieldId, string $value = null): void
    {
        //  wrapper begin
        echo '<tr>';

        //  header area ("left side")
        echo '<th scope="row">';
        //  label
        $this->renderLabel($fieldId);
        echo '</th>';

        //  input area ("right side")
        echo '<td>';
        //  the actual input field
        $this->renderField(null, $fieldId, $value);
        //  optional description/usage hint
        /**
         * styling with CSS class "custom-field-note"
         *
         * @see \Vierbeuter\WordPress\Feature\AddCustomPostTypes::admin_head()
         */
        $this->renderDescription($fieldId, 'custom-field-note clear');
        //  other… e.g. Javascript
        $this->renderAnythingAfterField(null, $fieldId, $value);
        echo '</td>';

        //  wrapper end
        echo '</tr>';
    }

    /**
     * Renders the markup of this custom-field for a ConfigPage supporting WPML.
     *
     * @param string $fieldId
     * @param string $langCode
     * @param string|null $value
     *
     * @see \Vierbeuter\WordPress\Feature\AdminPanel\AdminPage\WpmlWpOptionsPage
     */
    public function renderWpmlConfig(string $fieldId, string $langCode, string $value = null): void
    {
        //  wrapper begin
        echo '<tr>';

        //  header area ("left side")
        echo '<th scope="row">';
        //  label
        $this->renderLabel($fieldId, strtoupper($langCode));
        echo '</th>';

        //  input area ("right side")
        echo '<td>';
        //  the actual input field
        $this->renderField(null, $fieldId, $value);
        //  optional description/usage hint
        /**
         * styling with CSS class "custom-field-note"
         *
         * @see \Vierbeuter\WordPress\Feature\AddCustomPostTypes::admin_head()
         */
        $this->renderDescription($fieldId, 'custom-field-note clear');
        //  other… e.g. Javascript
        $this->renderAnythingAfterField(null, $fieldId, $value);
        echo '</td>';

        //  wrapper end
        echo '</tr>';
    }

    /**
     * Renders the label's markup.
     *
     * @param string $fieldId
     * @param string|null $labelAppendix
     */
    protected function renderLabel(string $fieldId, string $labelAppendix = null): void
    {
        $appendToLabel = !empty($labelAppendix) ? ' (' . $labelAppendix . ')' : '';

        echo '<label for="' . $fieldId . '">' . $this->label . $appendToLabel . '</label>';
    }

    /**
     * Renders the input's markup.
     *
     * @param \WP_Post|\WP_Term|null $postOrTerm
     * @param string $fieldId
     * @param string|null $value
     */
    abstract protected function renderField($postOrTerm = null, string $fieldId, string $value = null): void;

    /**
     * Renders the description's markup.
     *
     * @param string $fieldId
     * @param string $class
     */
    protected function renderDescription(string $fieldId, string $class = ''): void
    {
        if (!empty($this->description)) {
            echo '<p' . (empty($class) ? '' : ' class="' . $class . '"') . '>' . $this->description . '</p>';
        }
    }

    /**
     * Renders additional markup after the input to add Javascript snippets for instance or any other stuff like that.
     *
     * @param \WP_Post|\WP_Term|null $postOrTerm
     * @param string $fieldId
     * @param string|null $value
     */
    protected function renderAnythingAfterField($postOrTerm = null, string $fieldId, string $value = null): void
    {
        //  may be overridden
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
        return true;
    }

    /**
     * Returns a list of actions to be hooked into by this class. For each hook there <strong>must</strong> be defined a
     * public method with the same name as the hook (unless the hook's name consists of hyphens "-", for the appropriate
     * method name underscores "_" have to be used).
     *
     * Valid entries of the returned array are single strings, key-value-pairs and arrays. See comments in the method's
     * default implementation.
     *
     * @return string[]|array
     */
    protected function getActionHooks(): array
    {
        return [
            /** @see \Vierbeuter\WordPress\Feature\CustomField\CustomField::enqueueScripts() */
            'admin_enqueue_scripts' => 'enqueueScripts',
        ];
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
        //  may be overridden
    }
}
