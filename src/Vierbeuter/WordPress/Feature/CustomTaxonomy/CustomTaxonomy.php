<?php

namespace Vierbeuter\WordPress\Feature\CustomTaxonomy;

use Vierbeuter\WordPress\Service\Translator;

/**
 * The CustomTaxonomy class can be extended to define custom taxonomies.
 *
 * @package Vierbeuter\WordPress\Feature\CustomTaxonomy
 */
abstract class CustomTaxonomy
{

    /**
     * male / masculine
     *
     * @see \Vierbeuter\WordPress\Feature\CustomTaxonomy\CustomTaxonomy::getGender()
     */
    const GENDER_MALE = 'm';

    /**
     * feminine / female
     *
     * @see \Vierbeuter\WordPress\Feature\CustomTaxonomy\CustomTaxonomy::getGender()
     */
    const GENDER_FEMALE = 'f';

    /**
     * neuter / neutral / genderless
     *
     * @see \Vierbeuter\WordPress\Feature\CustomTaxonomy\CustomTaxonomy::getGender()
     */
    const GENDER_NEUTER = 'n';

    /**
     * both: female and/or male
     *
     * @see \Vierbeuter\WordPress\Feature\CustomTaxonomy\CustomTaxonomy::getGender()
     */
    const GENDER_FEMALE_AND_MALE = 'fm';

    /**
     * both: female and/or neuter
     *
     * @see \Vierbeuter\WordPress\Feature\CustomTaxonomy\CustomTaxonomy::getGender()
     */
    const GENDER_FEMALE_AND_NEUTER = 'fn';

    /**
     * both: male and/or neuter
     *
     * @see \Vierbeuter\WordPress\Feature\CustomTaxonomy\CustomTaxonomy::getGender()
     */
    const GENDER_MALE_AND_NEUTER = 'mn';

    /**
     * all: female, male and/or neuter
     *
     * @see \Vierbeuter\WordPress\Feature\CustomTaxonomy\CustomTaxonomy::getGender()
     */
    const GENDER_ALL = 'a';

    /**
     * @var \Vierbeuter\WordPress\Service\Translator
     */
    protected $translator;

    /**
     * @var \Vierbeuter\WordPress\Service\Translator
     */
    protected $vbTranslator;

    /**
     * @var array
     */
    protected $options;

    /**
     * CustomTaxonomy constructor.
     *
     * @param array $options
     *
     * @see https://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
     */
    function __construct(array $options = [])
    {
        //  set options as given, later on we apply them to also merge with default option values
        /** @see \Vierbeuter\WordPress\Feature\CustomTaxonomy\CustomTaxonomy::activate() */
        $this->options = $options;
    }

    /**
     * Activates the taxonomy.
     */
    public function activate(): void
    {
        //  taxonomy has to be activated instead of just putting this code into the constructor because we need to set
        //  a few things first before we can actually apply the options, for example
        //  one of those things is setting a translator, this is curently not possible during construction time
        //  so, the current process order is: construct, set stuff, activate
        /** @see \Vierbeuter\WordPress\Feature\AddCustomTaxonomies::activate() */

        $this->applyOptions($this->options);
    }

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
     * Returns the gender.
     *
     * Background: In some languages (such as German) the nouns are of a specific gender.
     * In case of doubt just returns the GENDER_ALL constant.
     *
     * @return string
     *
     * @see \Vierbeuter\WordPress\Feature\CustomTaxonomy\CustomTaxonomy::GENDER_MALE
     * @see \Vierbeuter\WordPress\Feature\CustomTaxonomy\CustomTaxonomy::GENDER_FEMALE
     * @see \Vierbeuter\WordPress\Feature\CustomTaxonomy\CustomTaxonomy::GENDER_NEUTER
     * @see \Vierbeuter\WordPress\Feature\CustomTaxonomy\CustomTaxonomy::GENDER_FEMALE_AND_MALE
     * @see \Vierbeuter\WordPress\Feature\CustomTaxonomy\CustomTaxonomy::GENDER_FEMALE_AND_NEUTER
     * @see \Vierbeuter\WordPress\Feature\CustomTaxonomy\CustomTaxonomy::GENDER_MALE_AND_NEUTER
     * @see \Vierbeuter\WordPress\Feature\CustomTaxonomy\CustomTaxonomy::GENDER_ALL
     */
    abstract public function getGender(): string;

    /**
     * Returns the translator.
     *
     * @return \Vierbeuter\WordPress\Service\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * Sets the translators.
     *
     * @param \Vierbeuter\WordPress\Service\Translator $translator
     * @param \Vierbeuter\WordPress\Service\Translator $vbTranslator
     */
    public function setTranslators(Translator $translator, Translator $vbTranslator): void
    {
        $this->translator = $translator;
        $this->vbTranslator = $vbTranslator;
    }

    /**
     * Translates the given text, optionally using the context string passed as second parameter.
     *
     * @param string $text
     * @param string|null $context
     *
     * @return string
     */
    public function translate(string $text, string $context = null): string
    {
        return $this->translator->translate($text, $context);
    }

    /**
     * Translates the given text using the vbTranslator, optionally using the context string passed as second parameter.
     *
     * To be used within core components only (unless you want to get untranslated texts as return value).
     *
     * @param string $text
     * @param string|null $context
     *
     * @return string
     */
    public function vbTranslate(string $text, string $context = null): string
    {
        return $this->vbTranslator->translate($text, $context);
    }

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
     * Applies the given $options array, missing keys will be added using a default value.
     *
     * @param array $options
     */
    private function applyOptions(array $options = []): void
    {
        //  merge default values with given array
        /** @see https://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments */
        $this->options = array_merge([
            'labels' => $this->getLabels($this->getLabelSingluar(), $this->getLabelPlural()),
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => true,
        ], $options);
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
            'search_items' => sprintf($this->vbTranslate('Search %s'), $labelPlural),
            'popular_items' => sprintf($this->vbTranslate('Popular %s'), $labelPlural),
            'all_items' => sprintf($this->vbTranslate('All %s'), $labelPlural),
            'edit_item' => sprintf($this->vbTranslate('Edit %s'), $labelSingular),
            'update_item' => sprintf($this->vbTranslate('Update %s'), $labelSingular),
            'add_new_item' => sprintf($this->vbTranslate('Add new %s', $this->getGender()), $labelSingular),
            'new_item_name' => sprintf($this->vbTranslate('New %s Name', $this->getGender()), $labelSingular),
            'separate_items_with_commas' => sprintf($this->vbTranslate('Separate %s with commas'), $labelPlural),
            'add_or_remove_items' => sprintf($this->vbTranslate('Add or remove %s'), $labelPlural),
            'choose_from_most_used' => sprintf($this->vbTranslate('Choose from the most used %s'), $labelPlural),
            'not_found' => sprintf($this->vbTranslate('No %s found'), $labelPlural),
            'menu_name' => ucfirst($labelPlural),
        ];
    }
}
