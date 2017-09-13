<?php

namespace Vierbeuter\WordPress\Feature\CustomField;

/**
 * Dropdown-selection to be used for a post-type's custom-field.
 *
 * @package Vierbeuter\WordPress\Feature\CustomField
 */
class Dropdown extends DropdownSelection
{

    /**
     * @var array
     */
    protected $selectionData;

    /**
     * Dropdown constructor. Requires a list of selections to be passed which is an associative array.
     *
     * Key-value-pairs of the given $selectionData array are as follows:
     * 'value' => 'label'
     *
     * The first entry is gonna be used as default value.
     *
     * @param string $slug
     * @param string $label
     * @param array $selectionData
     * @param string|null $description
     */
    function __construct(string $slug, string $label, array $selectionData, string $description = null)
    {
        parent::__construct($slug, $label, $description);

        $this->selectionData = $selectionData;
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
    protected function getSelectionData(): array
    {
        return $this->selectionData;
    }
}
