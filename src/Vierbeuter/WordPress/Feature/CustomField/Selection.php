<?php

namespace Vierbeuter\WordPress\Feature\CustomField;

/**
 * Selection-field to be used for a post-type's custom-field.
 *
 * @package Vierbeuter\WordPress\Feature\CustomField
 */
abstract class Selection extends CustomField
{

    /**
     * Renders the label's markup.
     *
     * @param \WP_Post|\WP_Term $postOrTerm
     * @param string $fieldId
     * @param string|null $value
     */
    protected function renderLabel($postOrTerm, string $fieldId, string $value = null): void
    {
        echo '<label>' . $this->label . '</label>';
    }

    /**
     * Returns an associative array with selectable data.
     *
     * Key-value-pairs are as follows:
     * 'value' => 'label'
     *
     * The first entry is gonna be used as default value.
     *
     * @return array
     */
    abstract protected function getSelectionData(): array;
}
