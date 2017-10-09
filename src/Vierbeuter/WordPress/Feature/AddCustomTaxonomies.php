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
            //  pass translators to taxonomy (for translating labels and buttons and such stuff)
            $taxonomy->setTranslators($this->getTranslator(), $this->getVbTranslator());
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
        $hooks = [
            //  hook for registering all taxonomies
            /** @see \Vierbeuter\WordPress\Feature\AddCustomTaxonomies::init() */
            'init',
            //  hooks for saving custom-field values
            /** @see \Vierbeuter\WordPress\Feature\AddCustomTaxonomies::create_term() */
            'create_term' => [
                'args' => 3,
            ],
            /** @see \Vierbeuter\WordPress\Feature\AddCustomTaxonomies::edit_terms() */
            'edit_terms' => [
                'args' => 2,
            ],
        ];

        //  iterate all taxonomies to add taxonomy-specific hooks
        foreach ($this->getTaxonomies() as $taxonomy) {
            //  hook for adding custom fields to "new taxonomy" form
            /** @see \Vierbeuter\WordPress\Feature\AddCustomTaxonomies::renderFormFieldsNew() */
            $hooks[$taxonomy->getSlug() . '_add_form_fields'] = 'renderFormFieldsNew';

            //  hook for adding custom fields to "edit taxonomy" form
            /** @see \Vierbeuter\WordPress\Feature\AddCustomTaxonomies::renderFormFieldsEdit() */
            $hooks[$taxonomy->getSlug() . '_edit_form_fields'] = [
                'method' => 'renderFormFieldsEdit',
                'args' => 2,
            ];
        }

        return $hooks;
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

    /**
     * Hooks into the same-named action hook to save values of custom-fields for new (freshly created) terms.
     *
     * @param int $termId
     * @param int $taxonomyId
     * @param string $taxonomySlug
     *
     * @see https://developer.wordpress.org/reference/hooks/create_term
     */
    public function create_term(int $termId, int $taxonomyId, string $taxonomySlug): void
    {
        $this->saveCustomFieldValues($termId, $taxonomySlug);
    }

    /**
     * Hooks into the same-named action hook to save values of custom-fields for edited (changed and updated) terms.
     *
     * @param int $termId
     * @param string $taxonomySlug
     *
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference/edit_terms
     */
    public function edit_terms(int $termId, string $taxonomySlug): void
    {
        $this->saveCustomFieldValues($termId, $taxonomySlug);
    }

    /**
     * Saves the given term's custom-field values.
     *
     * @param int $termId
     * @param string $taxonomySlug
     *
     * @see https://developer.wordpress.org/reference/functions/update_term_meta
     */
    protected function saveCustomFieldValues(int $termId, string $taxonomySlug): void
    {
        //  iterate all taxonomies
        /** @var CustomTaxonomy $taxonomy */
        foreach ($this->getTaxonomies() as $taxonomy) {

            //  only save field values for given taxonomy
            if ($taxonomy->getSlug() == $taxonomySlug) {

                //  save each field value
                /** @var \Vierbeuter\WordPress\Feature\CustomField\CustomField $field */
                foreach ($taxonomy->getFields() as $field) {
                    //  TODO: check nonce

                    //  get value of custom-field
                    $dbMetaKey = $this->getDbMetaKey($taxonomySlug, $field->getSlug());
                    $value = empty($_POST[$field->getSlug()]) ? null : $_POST[$field->getSlug()];

                    update_term_meta($termId, $dbMetaKey, $value);
                }

                //  stop iteration --> no other taxonomies to render fields for
                break;
            }
        }
    }

    /**
     * Renders the custom-fields for given taxonomy.
     *
     * @param string $taxonomySlug
     *
     * @see https://developer.wordpress.org/reference/hooks/taxonomy_add_form_fields/
     */
    public function renderFormFieldsNew(string $taxonomySlug): void
    {
        //  iterate all taxonomies
        /** @var CustomTaxonomy $taxonomy */
        foreach ($this->getTaxonomies() as $taxonomy) {

            //  only render fields for given taxonomy
            if ($taxonomy->getSlug() == $taxonomySlug) {

                //  render fields
                /** @var \Vierbeuter\WordPress\Feature\CustomField\CustomField $field */
                foreach ($taxonomy->getFields() as $field) {
                    //  TODO: add nonce field to be checked on save (saveTerm)
                    $field->renderTaxonomyNew($field->getSlug());
                }

                //  stop iteration --> no other taxonomies to render fields for
                break;
            }
        }
    }

    /**
     * Renders the custom-fields for given taxonomy.
     *
     * @param \WP_Term $term
     * @param string $taxonomySlug
     *
     * @see https://developer.wordpress.org/reference/hooks/taxonomy_edit_form_fields/
     * @see https://developer.wordpress.org/reference/functions/get_term_meta/
     */
    public function renderFormFieldsEdit(\WP_Term $term, string $taxonomySlug): void
    {
        //  iterate all taxonomies
        /** @var CustomTaxonomy $taxonomy */
        foreach ($this->getTaxonomies() as $taxonomy) {

            //  only render fields for given taxonomy
            if ($taxonomy->getSlug() == $taxonomySlug) {

                //  render fields
                /** @var \Vierbeuter\WordPress\Feature\CustomField\CustomField $field */
                foreach ($taxonomy->getFields() as $field) {
                    //  get value of custom-field
                    $dbMetaKey = $this->getDbMetaKey($taxonomySlug, $field->getSlug());
                    $value = get_term_meta($term->term_id, $dbMetaKey, true);

                    //  render field
                    $field->renderTaxonomyEdit($term, $field->getSlug(), $value);
                }

                //  stop iteration --> no other taxonomies to render fields for
                break;
            }
        }
    }

    /**
     * Returns the database key for accessing the meta-value.
     *
     * @param string $taxonomySlug
     * @param string $fieldSlug
     *
     * @return string
     */
    protected function getDbMetaKey(string $taxonomySlug, string $fieldSlug): string
    {
        //  concat slug of taxonomy with slug of custom-field
        return $taxonomySlug . '-' . $fieldSlug;
    }
}
