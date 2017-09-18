<?php

namespace Vierbeuter\WordPress\Feature\CustomPostType;

use Vierbeuter\WordPress\Service\Translator;

/**
 * The CustomPostType class can be extended to define custom post-types.
 *
 * @package Vierbeuter\WordPress\Feature\CustomPostType
 */
abstract class CustomPostType
{

    /**
     * key for accessing child posts
     *
     * @see \Vierbeuter\WordPress\Feature\CustomPostType\CustomPostType::getDataForExport()
     * @see \Vierbeuter\WordPress\Feature\CustomPostType\CustomPostType::getHierachicalDataForExport()
     */
    const CHILDREN = 'children';

    /**
     * key for accessing the order number
     *
     * @see \Vierbeuter\WordPress\Feature\CustomPostType\CustomPostType::getDataForExport()
     * @see \Vierbeuter\WordPress\Feature\CustomPostType\CustomPostType::getHierachicalDataForExport()
     */
    const MENU_ORDER = 'menu_order';

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
     * CustomPostType constructor.
     *
     * @param array $options
     *
     * @see http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
     */
    function __construct(array $options = [])
    {
        //  set options as given, later on we apply them to also merge with default option values
        /** @see \Vierbeuter\WordPress\Feature\CustomPostType\CustomPostType::activate() */
        $this->options = $options;
    }

    /**
     * Activates the post-type.
     */
    public function activate(): void
    {
        //  post-type has to be activated instead of just putting this code into the constructor because we need to set
        //  a few things first before we can actually apply the options, for example
        //  one of those things is setting a translator, this is curently not possible during construction time
        //  so, the current process order is: construct, set stuff, activate
        /** @see \Vierbeuter\WordPress\Feature\AddCustomPostTypes::activate() */

        $this->applyOptions($this->options);
    }

    /**
     * Returns the slug.
     *
     * @return string
     */
    abstract public function getSlug(): string;

    /**
     * Returns the post type's singular label.
     *
     * @return string
     */
    abstract public function getLabelSingluar(): string;

    /**
     * Returns the post type's plural label.
     *
     * @return string
     */
    abstract public function getLabelPlural(): string;

    /**
     * Returns the post type's description.
     *
     * @return string
     */
    abstract public function getDescription(): string;

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
     * Translates the given text.
     *
     * @param string $text
     *
     * @return string
     */
    public function translate(string $text): string
    {
        return $this->translator->translate($text);
    }

    /**
     * Translates the given text using the vbTranslator.
     *
     * To be used within core components only (unless you want to get untranslated texts as return value).
     *
     * @param string $text
     *
     * @return string
     */
    public function vbTranslate(string $text): string
    {
        return $this->vbTranslator->translate($text);
    }

    /**
     * Returns the configuration.
     *
     * @return array
     *
     * @see http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Returns the field groups including all custom fields.
     * Field groups correspond the sub-forms on a post's edit page in the admin panel.
     *
     * @return \Vierbeuter\WordPress\Feature\CustomField\FieldGroup[]
     */
    abstract public function getFieldGroups(): array;

    /**
     * Applies the given $options array, missing keys will be added using a default value.
     *
     * @param array $options
     */
    private function applyOptions(array $options = []): void
    {
        //  merge default values with given array
        /** @see https://codex.wordpress.org/Function_Reference/register_post_type#Arguments */
        $this->options = array_merge([
            'label' => $this->getLabelPlural(),
            'labels' => $this->getLabels($this->getLabelSingluar(), $this->getLabelPlural()),
            'hierarchical' => false,
            'description' => $this->getDescription(),
            'supports' => false,
            //['title', /*'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'any_custom_field',*/],
            'taxonomies' => [/*'category', 'post_tag', 'any_post_type'*/],
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_admin_bar' => true,
            'publicly_queryable' => true,
            'exclude_from_search' => false,
            'has_archive' => true,
            'query_var' => true,
            'can_export' => true,
            'rewrite' => true,
        ], $options);
    }

    /**
     * Returns an array with all post-type labels.
     *
     * @param string $labelSingular
     * @param string $labelPlural
     *
     * @return array
     */
    protected function getLabels(string $labelSingular, string $labelPlural): array
    {
        /** @see https://codex.wordpress.org/Function_Reference/register_post_type#Arguments */
        return [
            'name' => $labelPlural,
            'singular_name' => $labelSingular,
            'add_new' => $this->vbTranslate('Add new'),
            'add_new_item' => sprintf($this->vbTranslate('Add new %s'), $labelSingular),
            'edit_item' => sprintf($this->vbTranslate('Edit %s'), $labelSingular),
            'new_item' => sprintf($this->vbTranslate('New %s'), $labelSingular),
            'view_item' => sprintf($this->vbTranslate('View %s'), $labelSingular),
            'view_items' => sprintf($this->vbTranslate('View %s'), $labelPlural),
            'search_items' => sprintf($this->vbTranslate('Search %s'), $labelPlural),
            'not_found' => sprintf($this->vbTranslate('No %s found'), $labelPlural),
            'not_found_in_trash' => sprintf($this->vbTranslate('No %s found in Trash.'), $labelPlural),
            'parent_item_colon' => sprintf($this->vbTranslate('Parent %s:'), $labelSingular),
            'all_items' => sprintf($this->vbTranslate('All %s'), $labelPlural),
            'menu_name' => ucfirst($labelPlural),
        ];
    }

    /**
     * Determines which columns have to be displayed in admin panel. Also defines the columns' titles to be shown in
     * the table header.
     *
     * Is referenced in AddPostTypes::getFilterHooks() by callable.
     *
     * @param array $columns
     *
     * @return array
     *
     * @see \Vierbeuter\WordPress\Feature\AddCustomPostTypes::getFilterHooks()
     */
    public function getColumns(array $columns): array
    {
        //  by default the first columns are the checkbox (for bulk actions) and the title
        $columns = $this->getPrimaryColumns();

        //  columns to be added individually
        $columns = array_merge($columns, $this->getSecondaryColumns());

        //  by default the last column is the creation date
        $columns = array_merge($columns, $this->getTertiaryColumns());

        //  return column config
        return $columns;
    }

    /**
     * Returns the primary columns for the list view.
     *
     * @return array
     */
    public function getPrimaryColumns(): array
    {
        return [
            'cb' => '<input type="checkbox" />',
            'title' => $this->vbTranslate('Title'),
        ];
    }

    /**
     * Returns the columns to be displayed additionally in the list view of admin panel.
     *
     * The returned value is an associative array whose keys are column slugs (not those of the database fields or
     * custom-fields). The respective values are the columns' titles that are displayed in the table header.
     *
     * Example implementation for a adding new column showing referenced categories:
     * <code>
     * public function getSecondaryColumns(): array {
     *    return [
     *        'my-awesome-custom-column' => $this->$this->translate('Awesome data'),
     *    ];
     *}
     * </code>
     *
     * To let WordPress know what exactly has to be rendered as the new column's value for each post you have to
     * implement a new getter-method.
     *
     * Example implementation for such a getter:
     * <code>
     * public function getMyAwesomeCustomColumn(\WP_Post $post, array $customFields): string {
     *    return $this->getCustomFieldValue($customFields, $this->getSlug() . '-field_group_slug-field_slug');
     * }
     * </code>
     *
     * @return array
     */
    public function getSecondaryColumns(): array
    {
        return [];
    }

    /**
     * Returns the columns to be diplayed last in the list view of admin panel.
     *
     * @return array
     */
    public function getTertiaryColumns(): array
    {
        return [
            'date' => $this->vbTranslate('Date'),
        ];
    }

    /**
     * Returns a list of slugs of those custom fields that have to be used for fulltext search in admin panel.
     *
     * @return array
     */
    public function getCustomFieldSlugsForAdminSearch(): array
    {
        return [];
    }

    /**
     * Returns a list of columns that have to be sortable in admin panel.
     *
     * @return array
     */
    public function getSortableColumns(): array
    {
        return [];
    }

    /**
     * Returns the custom field vaule for given key (slug).
     *
     * @param array $customFields
     * @param string $customFieldSlug
     * @param string $default
     *
     * @return string
     */
    protected function getCustomFieldValue(array $customFields, string $customFieldSlug, string $default = '—'): string
    {
        return empty($customFields[$customFieldSlug][0]) ? $default : $customFields[$customFieldSlug][0];
    }

    /**
     * Returns a string representation for the boolean value of given custom field slug to be used for data export.
     *
     * @param array $customFields
     * @param string $customFieldSlug
     *
     * @return string
     */
    protected function getBooleanCustomFieldValueForExport(array $customFields, string $customFieldSlug): string
    {
        return !empty($customFields[$customFieldSlug][0]) && $customFields[$customFieldSlug][0] == 'true' ? 'X' : '';
    }

    /**
     * Returns the column value for given custom field containing a set of post-ids to be displayed in list view of
     * admin panel.
     *
     * The returned value is a comma-separated list of titles of the referenced posts (unless the ids are invalid, in
     * that case the ids are ignored).
     *
     * Separator for all list entries is a semicolon ";".
     *
     * @param \WP_Post $post
     * @param array $customFields
     * @param string $customFieldSlug
     * @param string $postTypeSlug
     * @param string $default
     *
     * @return string
     */
    protected function getColumnValueForReferencedPosts(
        \WP_Post $post,
        array $customFields,
        string $customFieldSlug,
        string $postTypeSlug,
        string $default = '—'
    ): string {
        $postTitles = [];

        //  id list for referenced posts as string
        $idsString = $this->getCustomFieldValue($customFields, $customFieldSlug);
        //  id list for referenced posts as array
        $postIds = explode(';', $idsString);

        //  iterate all ids
        foreach ($postIds as $postId) {
            //  get integer value (also for trimming and to ensure no invalid strings are given)
            $id = is_numeric($postId) ? intval($postId) : null;

            //  ensure to really have an ID (in case of $idsString is somthing like ", 123, , 125" etc.)
            if (empty($id)) {
                //  in that case just skip this ID which obviously is none
                continue;
            }

            //  load the post for given ID
            $referencedPost = get_post($id);

            //  if ID is correct
            if (!empty($referencedPost)) {
                //  get the post's title
                $postTitles[] = $referencedPost->post_title;
            }
        }

        //  return list of titles
        return empty($postTitles) ? $default : implode('; ', $postTitles);
    }

    /**
     * Returns the column value for given custom field containing the post's title to be displayed in list view of
     * admin panel.
     *
     * @param \WP_Post $post
     * @param array $customFields
     * @param string $customFieldSlug
     * @param string|mixed $default
     *
     * @return string
     */
    protected function getColumnValueForTitle(
        \WP_Post $post,
        array $customFields,
        string $customFieldSlug,
        string $default = '—'
    ): string {
        return '<strong><a class="row-title" href="/wp/wp-admin/post.php?post=' . $post->ID . '&action=edit">' . $this->getCustomFieldValue($customFields,
                $customFieldSlug, $default) . '</a></strong>';
    }

    /**
     * Returns the column value for given custom field containing an image URL to be displayed in list view of
     * admin panel.
     *
     * @param \WP_Post $post
     * @param array $customFields
     * @param string $customFieldSlug
     * @param string|mixed $default
     *
     * @return string
     */
    protected function getColumnValueForImageUrl(
        \WP_Post $post,
        array $customFields,
        string $customFieldSlug,
        string $default = '—'
    ): string {
        $image_url = $this->getCustomFieldValue($customFields, $customFieldSlug, '');

        return empty($image_url) ? $default : ('<img src="' . $image_url . '" width="100" />');
    }

    /**
     * Returns the column value for given taxonomy to be displayed in list view of admin panel.
     *
     * The returned value is a comma-separated list of titles of the referenced taxonomies.
     *
     * Separator for all list entries is a semicolon ";".
     *
     * @param \WP_Post $post
     * @param string $taxonomySlug
     * @param string $default
     *
     * @return string
     */
    protected function getColumnValueForTaxonomy(\WP_Post $post, string $taxonomySlug, string $default = '—'): string
    {
        //  determine the post's taxonomies for given slug
        $taxonomies = get_the_terms($post, $taxonomySlug);
        //  names of determined taxonomies
        $taxonomyNames = [];

        //  if taxonomies found for post
        if (!empty($taxonomies)) {

            //  iterate taxonomies
            foreach ($taxonomies as $color_group) {
                //  get the name
                $taxonomyNames[] = $color_group->name;
            }
        }

        //  return the taxonmies' names
        return empty($taxonomyNames) ? $default : implode('; ', $taxonomyNames);
    }

    /**
     * Determines if a post's title and slug have to be updated on saving it.
     *
     * @return bool
     *
     * @see \Vierbeuter\WordPress\Feature\AddCustomPostTypes::wp_insert_post_data()
     */
    public function updateTitleAndSlugOnSave(): bool
    {
        return true;
    }
}
