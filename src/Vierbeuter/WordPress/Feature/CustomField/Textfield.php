<?php

namespace Vierbeuter\WordPress\Feature\CustomField;

/**
 * Text-field to be used for a post-type's custom-field.
 *
 * @package Vierbeuter\WordPress\Feature\CustomField
 */
class Textfield extends CustomField
{

    /**
     * Renders the input's markup.
     *
     * @param string $fieldId
     * @param string|null $value
     * @param \WP_Post|\WP_Term|null $postOrTerm
     */
    protected function renderField(string $fieldId, string $value = null, $postOrTerm = null): void
    {
        echo '<input type="text" id="' . $fieldId . '" name="' . $fieldId . '" value="' . $value . '" />';
    }
}
