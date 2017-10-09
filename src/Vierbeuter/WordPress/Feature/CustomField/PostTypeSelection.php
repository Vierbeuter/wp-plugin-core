<?php

namespace Vierbeuter\WordPress\Feature\CustomField;

/**
 * Dropdown-selection with post-types to be used for a post-type's custom-field.
 *
 * @package Vierbeuter\WordPress\Feature\CustomField
 */
class PostTypeSelection extends DropdownSelection
{

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
    protected function getSelectionData(): array
    {
        $options = [
            '' => __('—', VBC_LANGUAGES_DOMAIN),
        ];

        foreach (get_post_types([], 'objects') as $post_type) {
            $options[$post_type->name] = $post_type->label;
        }

        return $options;
    }
}
