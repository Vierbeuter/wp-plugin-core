<?php

namespace Vierbeuter\WordPress\Feature\CustomField;

/**
 * Textarea to be used for a post-type's custom-field.
 *
 * @package Vierbeuter\WordPress\Feature\CustomField
 */
class Textarea extends CustomField
{

    /**
     * Renders the input's markup.
     *
     * @param \WP_Post|\WP_Term $postOrTerm
     * @param string $fieldId
     * @param string|null $value
     */
    protected function renderField($postOrTerm, string $fieldId, string $value = null): void
    {
        echo '<textarea id="' . $fieldId . '" name="' . $fieldId . '">' . $value . '</textarea>';
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
}
