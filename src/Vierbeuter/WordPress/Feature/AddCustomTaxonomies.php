<?php

namespace Vierbeuter\WordPress\Feature;

use Vierbeuter\WordPress\Feature\CustomTaxonomy\CustomTaxonomy;

/**
 * The AddCustomTaxonomies feature adds custom taxonomies to WordPress.
 *
 * @package Vierbeuter\WordPress\Feature
 */
abstract class AddCustomTaxonomies extends Feature
{

    /**
     * @var CustomTaxonomy[]
     */
    private $taxonomies;

    /**
     * AddCustomTaxonomies constructor.
     */
    public function __construct()
    {
        //  initialize empty list --> will be filled on activate()
        //  no translator available yet here, for example … 
        //  that's why the taxonomies are not defined here but in activate()
        /** @see \Vierbeuter\WordPress\Feature\AddCustomTaxonomies::activate() */
        $this->taxonomies = [];
    }

    /**
     * Activates the feature to actually extend WP functionality.
     */
    public function activate(): void
    {
        //  first of all get the taxonomies to be registered from sub-class
        foreach ($this->initTaxonomies() as $taxonomy) {
            //  pass translator to taxonomy (for translating labels and buttons and such stuff)
            $taxonomy->setTranslator($this->getTranslator());
            //  activate the taxonomy
            $taxonomy->activate();
            //  keep in mind the current taxonomy using its slug as key
            //  taxonomies will be accessed later, see WordPress hooks
            $this->taxonomies[$taxonomy->getSlug()] = $taxonomy;
        }

        parent::activate();
    }

    /**
     * Returns the list of taxonomies to be registered.
     *
     * @return CustomTaxonomy[]
     */
    abstract protected function initTaxonomies(): array;

    /**
     * Returns the list of registered taxonomies.
     *
     * @return CustomTaxonomy[]
     */
    protected function getTaxonomies(): array
    {
        return $this->taxonomies;
    }

    /**
     * Returns a list of actions to be hooked into by this class. For each hook there <strong>must</strong> be defined a
     * public method with the same name as the hook (unless the hook's name consists of hyphens "-", for the appropriate
     * method name underscores "_" have to be used).
     *
     * Valid entries of the returned array are single strings, key-value-pairs and arrays. See comments in the method's
     * default implementation.
     *
     * @return string[]|array
     */
    protected function getActionHooks(): array
    {
        return [
            /** @see \Vierbeuter\WordPress\Feature\AddCustomTaxonomies::init() */
            'init',
        ];
    }

    /**
     * Hooks into the same-named action hook to register the taxonomies.
     *
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference/init
     * @see https://codex.wordpress.org/Function_Reference/register_taxonomy
     */
    public function init(): void
    {
        //  iterate all taxonomies
        foreach ($this->getTaxonomies() as $taxonomy) {
            //  register each taxonomy
            register_taxonomy($taxonomy->getSlug(), $taxonomy->getPostTypeSlugs(), $taxonomy->getOptions());
        }
    }
}
