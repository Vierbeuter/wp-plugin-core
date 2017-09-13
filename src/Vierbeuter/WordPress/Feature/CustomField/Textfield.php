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
     * @param \WP_Post $post
     * @param string $fieldId
     * @param string|null $value
     */
    protected function renderField(\WP_Post $post, string $fieldId, string $value = null): void
    {
        echo '<input type="text" id="' . $fieldId . '" name="' . $fieldId . '" value="' . $value . '" />';
    }
}
