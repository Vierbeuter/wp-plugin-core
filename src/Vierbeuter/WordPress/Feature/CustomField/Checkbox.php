<?php

namespace Vierbeuter\WordPress\Feature\CustomField;

/**
 * Checkbox to be used for a post-type's custom-field.
 *
 * @package Vierbeuter\WordPress\Feature\CustomField
 */
class Checkbox extends CustomField
{

    /**
     * @var string|null
     */
    protected $checkboxLabel;

    /**
     * Checkbox constructor.
     *
     * @param string $slug
     * @param string $label
     * @param string|null $checkboxLabel
     * @param string|null $description
     */
    function __construct(string $slug, string $label, string $checkboxLabel = null, string $description = null)
    {
        parent::__construct($slug, $label, $description);

        $this->checkboxLabel = $checkboxLabel;
    }

    /**
     * Renders the input's markup.
     *
     * @param \WP_Post|\WP_Term $postOrTerm
     * @param string $fieldId
     * @param string|null $value
     */
    function renderField($postOrTerm, string $fieldId, string $value = null): void
    {
        $checked = $value == 'true' ? 'checked="checked"' : '';

        //  markup of checkbox and hidden field (latter one as default value in case of checkbox is not checked)
        echo '<input type="hidden" name="' . $fieldId . '" value="false" />';
        echo '<input type="checkbox" id="' . $fieldId . '" name="' . $fieldId . '" value="true" ' . $checked . ' />';

        //  optionally render an additional label directly next to the checkbox
        if (!empty($this->checkboxLabel)) {
            echo ' <label for="' . $fieldId . '">' . $this->checkboxLabel . '</label>';
        }
    }
}
