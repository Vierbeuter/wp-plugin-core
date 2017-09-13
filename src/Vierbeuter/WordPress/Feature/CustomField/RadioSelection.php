<?php

namespace Vierbeuter\WordPress\Feature\CustomField;

/**
 * Bunch of radio-buttons to be used for a post-type's custom-field.
 *
 * @package Vierbeuter\WordPress\Feature\CustomField
 */
abstract class RadioSelection extends Selection
{

    /**
     * Renders the input's markup.
     *
     * @param \WP_Post $post
     * @param string $fieldId
     * @param string|null $value
     */
    function renderField(\WP_Post $post, string $fieldId, string $value = null): void
    {
        echo '<ul>';

        //  grab data for radios
        $selectionData = $this->getSelectionData();

        //  if no value given
        if (empty($value)) {
            //  first value of $selectionData is the one to use (as default)
            reset($selectionData);
            $value = key($selectionData);
        }

        //  iterate all data entries
        foreach ($selectionData as $radioValue => $radioLabel) {
            //  build field-id of current radio-button (for labelling it with <label for="…">)
            $radioId = $fieldId . '-' . $radioValue;
            //  check if current radio-button has to be selected by default
            $selected = $radioValue == $value ? 'checked="checked"' : '';

            //  render the radio-button
            echo '<li>';
            echo '<input type="radio" id="' . $radioId . '" name="' . $fieldId . '" value="' . $radioValue . '" ' . $selected . '/> ';
            echo '<label for="' . $radioId . '">' . $radioLabel . '</label>';
            echo '</li>';
        }

        echo '</ul>';
    }
}
