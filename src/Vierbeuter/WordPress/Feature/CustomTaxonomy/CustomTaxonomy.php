<?php

namespace Vierbeuter\WordPress\Feature\CustomTaxonomy;

use Vierbeuter\WordPress\Di\Component;
use Vierbeuter\WordPress\Feature\Traits\HasWpHookSupport;
use Vierbeuter\WordPress\Traits\HasTranslatorSupport;

/**
 * The CustomTaxonomy class can be extended to define custom taxonomies.
 *
 * @package Vierbeuter\WordPress\Feature\CustomTaxonomy
 */
abstract class CustomTaxonomy extends Component
{

    /**
     * @var array
     */
    protected $options;

    /**
     * include methods for translating texts
     */
    use HasTranslatorSupport;
    /**
     * include methods for being able to provide WP-hook implementations
     */
    use HasWpHookSupport;

    /**
     * Activates the taxonomy.
     *
     * @see \Vierbeuter\WordPress\Feature\AddCustomTaxonomies::activate()
     */
    public function activate(): void
    {
        //  apply options (merge default values with array from sub-class)
        $this->options = array_merge($this->getDefaultOptions(), $this->getTaxonomyOptions());
        //  register implementations of WP-hooks
        $this->initWpHooks();

        //  let all fields register their WP-hook implementations
        foreach ($this->getFields() as $field) {
            $field->initWpHooks();
        }
    }

    /**
     * Returns the default options for this taxonomy.
     *
     * Will be merged with taxonomy specific options as defined in getTaxonomyOptions() method.
     *
     * @return array
     *
     * @see \Vierbeuter\WordPress\Feature\CustomTaxonomy\CustomTaxonomy::getTaxonomyOptions()
     * @see https://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
     */
    protected function getDefaultOptions(): array
    {
        return [
            'labels' => $this->getLabels($this->getLabelSingluar(), $this->getLabelPlural()),
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => true,
        ];
    }

    /**
     * Returns taxonomy specific options to extend or override the default options as defined in getDefaultOptions()
     * method.
     *
     * @return array
     *
     * @see \Vierbeuter\WordPress\Feature\CustomTaxonomy\CustomTaxonomy::getDefaultOptions()
     * @see https://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
     */
    abstract protected function getTaxonomyOptions(): array;

    /**
     * Returns the slug.
     *
     * @return string
     */
    abstract public function getSlug(): string;

    /**
     * Returns the taxonomy's singular label.
     *
     * @return string
     */
    abstract public function getLabelSingluar(): string;

    /**
     * Returns the taxonomy's plural label.
     *
     * @return string
     */
    abstract public function getLabelPlural(): string;

    /**
     * Returns the taxonomy's description.
     *
     * @return string
     */
    abstract public function getDescription(): string;

    /**
     * Returns the slugs of the post-types using this taxonomy.
     *
     * @return string[]
     */
    abstract public function getPostTypeSlugs(): array;

    /**
     * Returns the configuration.
     *
     * @return array
     *
     * @see https://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Returns an array with all taxonomy labels.
     *
     * @param string $labelSingular
     * @param string $labelPlural
     *
     * @return array
     */
    protected function getLabels(string $labelSingular, string $labelPlural): array
    {
        /** @see https://codex.wordpress.org/Function_Reference/register_taxonomy#Example */
        return [
            'name' => $labelPlural,
            'singular_name' => $labelSingular,
            'search_items' => sprintf($this->translate('Search %s', true), $labelPlural),
            'popular_items' => sprintf($this->translate('Popular %s', true), $labelPlural),
            'all_items' => sprintf($this->translate('All %s', true), $labelPlural),
            'edit_item' => sprintf($this->translate('Edit %s', true), $labelSingular),
            'update_item' => sprintf($this->translate('Update %s', true), $labelSingular),
            'add_new_item' => sprintf($this->translate('Add new %s', true), $labelSingular),
            'new_item_name' => sprintf($this->translate('New %s Name', true), $labelSingular),
            'separate_items_with_commas' => sprintf($this->translate('Separate %s with commas', true), $labelPlural),
            'add_or_remove_items' => sprintf($this->translate('Add or remove %s', true), $labelPlural),
            'choose_from_most_used' => sprintf($this->translate('Choose from the most used %s', true), $labelPlural),
            'not_found' => sprintf($this->translate('No %s found', true), $labelPlural),
            'menu_name' => ucfirst($labelPlural),
        ];
    }

    /**
     * Returns all custom fields.
     *
     * @return \Vierbeuter\WordPress\Feature\CustomField\CustomField[]
     */
    abstract public function getFields(): array;
}
