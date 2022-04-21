<?php

namespace Vierbeuter\WordPress\Feature\CustomField;

/**
 * Number-field to be used for a post-type's numeric custom-field.
 *
 * @package Vierbeuter\WordPress\Feature\CustomField
 */
class NumberField extends CustomField
{

    /**
     * @var int
     */
    protected $min;

    /**
     * @var int
     */
    protected $max;

    /**
     * @var int
     */
    protected $step;

    /**
     * NumberField constructor.
     *
     * @param string $slug
     * @param string $label
     * @param string|null $description
     * @param int $min
     * @param int $max
     * @param int $step
     */
    function __construct(
        string $slug,
        string $label,
        string $description = null,
        int $min = 0,
        int $max = PHP_INT_MAX,
        int $step = 1
    ) {
        parent::__construct($slug, $label, $description);

        $this->min = $min;
        $this->max = $max;
        $this->step = $step;
    }

    /**
     * Renders the input's markup.
     *
     * @param string $fieldId
     * @param string|null $value
     * @param \WP_Post|\WP_Term|null $postOrTerm
     */
    function renderField(string $fieldId, string $value = null, $postOrTerm = null): void
    {
        echo '<input type="number" id="' . $fieldId . '" name="' . $fieldId . '" value="' . (empty($value) ? '0' : $value) . '" min="' . $this->min . '" max="' . $this->max . '" step="' . $this->step . '" />';
    }
}
