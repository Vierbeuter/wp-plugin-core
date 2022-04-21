<?php

namespace Vierbeuter\WordPress\Feature\CustomField;

/**
 * Non-editable ("read only") field to be used for a post-type's custom-field.
 *
 * @package Vierbeuter\WordPress\Feature\CustomField
 */
class ReadOnlyField extends CustomField
{

    /**
     * Renders the label's markup.
     *
     * @param string $fieldId
     * @param string|null $labelAppendix
     */
    protected function renderLabel(string $fieldId, string $labelAppendix = null): void
    {
        $appendToLabel = !empty($labelAppendix) ? ' (' . $labelAppendix . ')' : '';

        echo '<label>' . $this->label . $appendToLabel . '</label>';
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
        echo '<span class="custom-field-note">– nicht editierbar –</span>';
        echo '<input type="hidden" name="' . $fieldId . '" value="' . htmlentities($value) . '" />';

        //  try to decode JSON
        $jsonDecodedValue = json_decode($value, true);

        //  if decoding succeeded
        if (!empty($jsonDecodedValue)) {
            //  then our value is a JSON-encoded string, but for displaying it we'll use the actual value
            //  (which is the the result from JSON-decoding)
            $value = $jsonDecodedValue;
        }

        echo empty($value) ? '' : (is_array($value) ? ('<pre>' . print_r($value, true) . '</pre>') : ('<br />' . $value));
    }
}
