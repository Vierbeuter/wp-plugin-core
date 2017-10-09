<?php

namespace Vierbeuter\WordPress\Feature\CustomField;

/**
 * Dropdown-selection to be used for a post-type's custom-field.
 *
 * @package Vierbeuter\WordPress\Feature\CustomField
 */
abstract class DropdownSelection extends Selection
{

    /**
     * Renders the input's markup.
     *
     * @param \WP_Post|\WP_Term|null $postOrTerm
     * @param string $fieldId
     * @param string|null $value
     */
    function renderField($postOrTerm = null, string $fieldId, string $value = null): void
    {
        echo '<select id="' . $fieldId . '" name="' . $fieldId . '" size="1">';

        //  get data for selection
        $selectionData = $this->getSelectionData();

        //  if no value given
        if (empty($value)) {
            //  first value of $selectionData is the one to use (as default)
            reset($selectionData);
            $value = key($selectionData);
        }

        //  iterate all data entries
        foreach ($selectionData as $optionValue => $optionLabel) {
            //  check if current option-entry has to be selected by default
            $selected = $optionValue == $value ? 'selected="selected"' : '';

            //  render the option
            echo '<option value="' . $optionValue . '" ' . $selected . '>' . $optionLabel . '</option>';
        }

        echo '</select>';
    }
}
