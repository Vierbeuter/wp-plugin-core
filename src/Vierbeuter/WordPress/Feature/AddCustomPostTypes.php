<?php

namespace Vierbeuter\WordPress\Feature;

use Vierbeuter\WordPress\Feature\CustomPostType\CustomPostType;

/**
 * The AddCustomPostTypes feature adds custom post-types to WordPress.
 *
 * @package Vierbeuter\WordPress\Feature
 */
abstract class AddCustomPostTypes extends Feature
{

    /**
     * @var CustomPostType[]
     */
    private $postTypes;

    /**
     * AddCustomPostTypes constructor.
     */
    public function __construct()
    {
        //  initialize empty list --> will be filled on activate()
        //  no translator available yet here, for example … 
        //  that's why the post-types are not defined here but in activate()
        /** @see \Vierbeuter\WordPress\Feature\AddCustomPostTypes::activate() */
        $this->postTypes = [];
    }

    /**
     * Activates the feature to actually extend WP functionality.
     */
    public function activate(): void
    {
        //  first of all get the post-types to be registered from sub-class
        foreach ($this->getPostTypeClasses() as $postTypeClass) {
            //  given class must not be empty
            if (empty($postTypeClass)) {
                throw new \Exception('Given post-type class is empty. Please check implementation of ' . get_called_class() . '->getPostTypeClasses() method.');
            }

            //  if array given then first entry is the post-type class, the rest are parameter names for this post-type
            if (is_array($postTypeClass)) {
                $paramNames = $postTypeClass;
                $postTypeClass = array_shift($paramNames);
            }

            //  check post-type for extending the correct base class
            if (!is_string($postTypeClass) || !is_subclass_of($postTypeClass, CustomPostType::class)) {
                throw new \Exception('Invalid class "' . $postTypeClass . '" given, needs to be a sub-class of "' . CustomPostType::class . '". Also check implementation of ' . get_called_class() . '->getPostTypeClasses() method.');
            }

            //  add class to DI-container
            $this->addComponent($postTypeClass, ...(empty($paramNames) ? [] : $paramNames));
            //  get post-type instance via DI
            /** @var \Vierbeuter\WordPress\Feature\CustomPostType\CustomPostType $postType */
            $postType = $this->getComponent($postTypeClass);

            //  activate the post-type
            $postType->activate();
            //  keep in mind the current post-type using its slug as key
            //  post-types will be accessed later, see WordPress hooks
            $this->postTypes[$postType->getSlug()] = $postType;
        }

        parent::activate();
    }

    /**
     * Returns a list of post-type class names. Each post-type class has to be a sub-class of CustomPostType, will be
     * checked on feature activation.
     *
     * @return string[]
     *
     * @see \Vierbeuter\WordPress\Feature\CustomPostType\CustomPostType
     * @see \Vierbeuter\WordPress\Feature\AddCustomPostTypes::activate()
     */
    abstract protected function getPostTypeClasses(): array;

    /**
     * Returns the list of registered post-types.
     *
     * @return CustomPostType[]
     */
    public function getPostTypes(): array
    {
        return $this->postTypes;
    }

    /**
     * Returns the post-type for given slug.
     *
     * @param string $postTypeSlug
     *
     * @return null|CustomPostType
     */
    public function getPostType(string $postTypeSlug): ?CustomPostType
    {
        $postTypes = $this->getPostTypes();

        return empty($postTypes[$postTypeSlug]) ? null : $postTypes[$postTypeSlug];
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
            /** @see \Vierbeuter\WordPress\Feature\AddCustomPostTypes::init() */
            'init',
            /** @see \Vierbeuter\WordPress\Feature\AddCustomPostTypes::add_meta_boxes() */
            'add_meta_boxes',
            /** @see \Vierbeuter\WordPress\Feature\AddCustomPostTypes::save_post() */
            'save_post',
            /** @see \Vierbeuter\WordPress\Feature\AddCustomPostTypes::manage_posts_custom_column() */
            'manage_posts_custom_column',        //  for non-hierarchical post-types
            /** @see \Vierbeuter\WordPress\Feature\AddCustomPostTypes::manage_pages_custom_column() */
            'manage_pages_custom_column' => [    //  for hierarchical/nestable post-types
                'args' => 2,
            ],
            /** @see \Vierbeuter\WordPress\Feature\AddCustomPostTypes::admin_head() */
            'admin_head',
            /** @see \Vierbeuter\WordPress\Feature\AddCustomPostTypes::filterRevisionFields() */
            '_wp_post_revision_fields' => [
                'method' => 'filterRevisionFields',
                'args' => 2,
            ],
        ];
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
    protected function getFilterHooks(): array
    {
        $filter_hooks = [
            /** @see \Vierbeuter\WordPress\Feature\AddCustomPostTypes::pre_get_posts() */
            'pre_get_posts',
            /** @see \Vierbeuter\WordPress\Feature\AddCustomPostTypes::wp_insert_post_data() */
            'wp_insert_post_data' => [
                'args' => 2,
            ],
        ];

        //  iterate all post-types to hook into post-type-specific filter hooks
        foreach ($this->getPostTypes() as $postType) {
            //  define columns to be displayed in list view
            /** @see \Vierbeuter\WordPress\Feature\CustomPostType\CustomPostType::getColumns() */
            add_filter('manage_edit-' . $postType->getSlug() . '_columns', [$postType, 'getColumns']);
            //  define columns to be sortable in list view
            /** @see \Vierbeuter\WordPress\Feature\CustomPostType\CustomPostType::getSortableColumns() */
            add_filter('manage_edit-' . $postType->getSlug() . '_sortable_columns',
                [$postType, 'getSortableColumns']);
        }

        return $filter_hooks;
    }

    /**
     * Hooks into the same-named action hook to register the post-types.
     */
    public function init(): void
    {
        //  iterate all post-types
        foreach ($this->getPostTypes() as $postType) {
            //  register each post-type
            /** @see https://codex.wordpress.org/Function_Reference/register_post_type */
            register_post_type($postType->getSlug(), $postType->getOptions());
        }
    }

    /**
     * Hooks into the same-named action hook to register field-groups and custom-fields to the post-types.
     */
    public function add_meta_boxes(): void
    {
        //  iterate all post-types to register meta-fields for each
        foreach ($this->getPostTypes() as $postType) {
            //  get the field-groups
            $fieldGroups = $postType->getFieldGroups();

            //  iterate the field-groups
            foreach ($fieldGroups as $fieldGroup) {
                $fieldGroup->setPostType($postType);

                //  register field-group to current post-type to render its form-fields
                $fieldGroup->register();
            }
        }
    }

    /**
     * Hooks into the same-named action hook to apply the custom-field-values as POSTed and to save them.
     *
     * @param int $postId
     */
    public function save_post(int $postId): void
    {
        //  determine requested post-type-slug
        $postTypeSlug = empty($_REQUEST['post_type']) ? null : $_REQUEST['post_type'];
        //  try to get the corresponding post-type
        $postType = empty($postTypeSlug) ? null : $this->getPostType($postTypeSlug);

        //  only if post-type found
        if (!empty($postType)) {
            //  iterate the post-type's field-groups
            foreach ($postType->getFieldGroups() as $fieldGroup) {
                $fieldGroup->setPostType($postType);

                //  save form data for each custom-field of current field-group
                $fieldGroup->save($postType->getSlug(), $postId);
            }
        }
    }

    /**
     * Hooks into the same-named action hook to determine and render the values of individually added columns to the
     * list views of the admin-panel (columns that have been added by using CustomPostType->getColumns() method).
     *
     * This hook is used for non-hierarchical post-types.
     *
     * @param string $column
     *
     * @see \Vierbeuter\WordPress\Feature\AddCustomPostTypes::manage_pages_custom_column()
     * @see \Vierbeuter\WordPress\Feature\CustomPostType\CustomPostType::getColumns()
     */
    public function manage_posts_custom_column(string $column): void
    {
        global $post;
        $postType = $this->getPostType($post->post_type);
        //  build the name of getter-method for given column
        $columnParts = explode('-', $column);
        $columnPartsUcFirst = array_map(function (string $columnPart) {
            return ucfirst($columnPart);
        }, $columnParts);
        $columnCamelCase = implode('', $columnPartsUcFirst);
        $getMethod = 'get' . $columnCamelCase;

        if (method_exists($postType, $getMethod)) {
            //  if method exists

            //  load values of custom-fields
            $customFields = get_post_custom();
            //  invoke getter-method
            echo $postType->$getMethod($post, $customFields);
        } else {
            //  method not found

            //  make notice to the developer for missing method, also offer example implementation
            echo sprintf($this->translate('The column <code>%1$s</code> is unknown, no value to be rendered. Please implement <code>%2$s</code> method to determine a string-representation of the column value.',
                    true), $column, get_class($postType) . '->' . $getMethod . '()') . '<br />';
            echo $this->translate('In case of a standard custom-field the implementation may look like the following:',
                true);
            echo "<pre>/**\n";
            echo " * Returns the value for column '$column' as string.\n";
            echo " * \n";
            echo " * @param \\WP_Post \$post\n";
            echo " * @param array \$customFields\n";
            echo " * \n";
            echo " * @return string\n";
            echo " * \n";
            echo " * @see getSecondaryColumns()\n";
            echo " */\n";
            echo "public function $getMethod(\\WP_Post \$post, array \$customFields): string {\n";
            echo "    return \$this->getCustomFieldValue(\$customFields, \$this->getSlug() . '-field_group_slug-field_slug');\n";
            echo "}</pre>";
            echo sprintf($this->translate('You can also use the <code>%1$s</code>-methods to render field values that are non-standard respectively of a specific type or kind such as the title and an image URL.',
                    true), 'getColumnValueFor…') . '<br />';
        }
    }

    /**
     * Hooks into the same-named action hook to determine and render the values of individually added columns to the
     * list views of the admin-panel (columns that have been added by using CustomPostType->getColumns() method).
     *
     * This hook is used for hierarchical/nestable post-types.
     *
     * @param string $column
     * @param int $postId
     *
     * @see \Vierbeuter\WordPress\Feature\AddCustomPostTypes::manage_posts_custom_column()
     */
    public function manage_pages_custom_column(string $column, int $postId): void
    {
        self::manage_posts_custom_column($column);
    }

    /**
     * Hooks into the same-named filter hook to manipulate the given query to select posts with.
     *
     * @param \WP_Query $query
     */
    public function pre_get_posts(\WP_Query $query): void
    {
        //  don't alter the query if no post-type requested
        if (empty($query->query['post_type'])) {
            return;
        }

        //  determine post-types
        $postTypeSlugs = is_array($query->query['post_type']) ? $query->query['post_type'] : [$query->query['post_type']];

        foreach ($postTypeSlugs as $postTypeSlug) {
            $postType = $this->getPostType($postTypeSlug);

            //  if no post-type found then let the given query untouched
            if (empty($postType)) {
                continue;
            }

            //  extend fulltext-search in admin-panel
            $this->extendFulltextSearchForAdminPanel($query, $postType);

            //  let the post-type manipulate the query on its own
            $postType->preGetPosts($query);
        }
    }

    /**
     * Extends the fulltext-search when being in admin-panel for also searching custom-fields of given post-type
     * instead of searching for title and content only. The list of custom-fields to search by can be defined using the
     * <code>getCustomFieldSlugsForAdminSearch()</code> method.
     *
     * @param \WP_Query $query
     * @param \Vierbeuter\WordPress\Feature\CustomPostType\CustomPostType $postType
     *
     * @see \Vierbeuter\WordPress\Feature\CustomPostType\CustomPostType::getCustomFieldSlugsForAdminSearch()
     * @see http://wordpress.stackexchange.com/questions/11758/extending-the-search-context-in-the-admin-list-post-screen
     */
    protected function extendFulltextSearchForAdminPanel(\WP_Query $query, CustomPostType $postType): void
    {
        //  only change database-queries that are made for a search in the admin panel
        if ($query->is_search() && $query->is_admin) {
            //  get searchable custom-fields for requested post-type
            $customFieldSlugs = $postType->getCustomFieldSlugsForAdminSearch();
            //  get the search-term from GET parameters
            $searchTerm = $query->query_vars['s'];

            //  only if search-term given and custom-fields enabled for fulltext-search
            if (!empty($searchTerm) && !empty($customFieldSlugs)) {
                //  prepare meta-query for selecting db-entries by custom-fields
                $metaQuery = ['relation' => 'OR'];

                //  add conditions to the meta-query for each searchable custom-field
                foreach ($customFieldSlugs as $customFieldSlug) {
                    array_push($metaQuery, [
                        'key' => $customFieldSlug,
                        'value' => $searchTerm,
                        'compare' => 'LIKE',
                    ]);
                }

                //  apply meta-query definitions to the given "main" query
                $query->set('meta_query', $metaQuery);

                //  workaround for some unwanted behaviour of the posts search:
                //  hook into filter hook to deactivate fulltext-search on title and content fields
                //  otherwise no search results can be found at all
                //  (see docs of posts_search() method for more information)
                /** @see \Vierbeuter\WordPress\Feature\AddCustomPostTypes::posts_search() */
                add_filter('posts_search', [$this, 'posts_search']);
            };
        }
    }

    /**
     * Hooks into the same-named filter hook to deactivate the fulltext-search for title and content fields.
     *
     * The search will only be deactivated for the fields post_title and post_content. Furthermore this only happens
     * for a search from within the admin panel and only if the requested post-type has defined custom-fields being
     * searchable in the admin panel.
     *
     * See also pre_get_posts().
     *
     * The whole thing is nothing more than a workaround, for details on what and why have a look to the source
     * documentation in the method body.
     *
     * @param string $searchCondition
     *
     * @return string
     *
     * @see \Vierbeuter\WordPress\Feature\AddCustomPostTypes::pre_get_posts()
     */
    public function posts_search(string $searchCondition): string
    {
        /*
         * $searchCondition contains something like this:
         * " AND (((wp_posts.post_title LIKE '%search input%') OR (wp_posts.post_content LIKE '%search input%'))) "
         *
         * Therefore the term will be searched in the fields post_title and post_content. No problem so far.
         * But the query will also be JOINed with the wp_postmeta table to search the term in one of the custom-fields.
         * This means that post_title respectively post_content must be identical to the meta-field to be able to find
         * that post entry.
         *
         * Because the resulting SQL query will kinda look like this:
         *
         * … WHERE (
         *   (wp_posts.post_title LIKE '%search input%') OR
         *   (wp_posts.post_content LIKE '%search input%')
         * ) AND (
         *   (wp_postmeta.meta_key = 'custom-field-name' AND
         *   CAST(wp_postmeta.meta_value AS CHAR) LIKE '%search input%')
         * )
         *
         * That's why we have to remove the post's title and content from that SQL condition.
         *
         * The fields stay searchable if they are duplicated in hidden custom-fields though.
         */

        return '';
    }

    /**
     * Hooks into the same-named action hook to add some CSS styles to the admin panel.
     */
    public function admin_head(): void
    {
        ?>
		<style type="text/css">
			/* hints and descriptions for custom-fields */
			.form-table td p.custom-field-note, .form-table td span.custom-field-note {
				font-size: 90%;
				color: #555;
			}

			/* code-snippets in hints and descriptions of custom-fields */
			.form-table td p.custom-field-note code {
				font-size: 90%;
			}
		</style><?php
    }

    /**
     * Hooks into the same-named filter hook to automatically update a post's title and slug on save.
     *
     * To set the title and slug the value of the very first custom-field will be used.
     *
     * @param array $data the data of a custom post-type
     * @param array $postAttr array of sanitized, but otherwise unmodified post data
     *
     * @return array
     *
     * @see CustomPostType::updateTitleAndSlugOnSave()
     */
    public function wp_insert_post_data(array $data, array $postAttr)
    {
        //  get slug of requested post-type
        $requestedPostTypeSlug = $data['post_type'];

        //  only on saving a post from within its edit-page (not on quick-edit from within list view since there are meta-fields mssing)
        if (!empty($postAttr['action']) && $postAttr['action'] == 'editpost') {
            //  determine the post-type
            $postType = $this->getPostType($requestedPostTypeSlug);

            //  if post-type found and if automatic update of title and slug allowed for this post-type
            if (!empty($postType) && $postType->updateTitleAndSlugOnSave()) {
                //  get post-type config
                $postTypeOptions = $postType->getOptions();

                //  check if no title field configured for this post-type
                //  --> 'cause only if title field is missing we want to update it (and therefore the slug) automatically
                if (empty($postTypeOptions['supports']) || !in_array('title', $postTypeOptions['supports'])) {
                    //  ID of the first custom-field that can be found
                    //  --> the title is gonna be filled using that one custom-field
                    $firstFieldId = null;

                    //  iterate all field-groups (in case of first group has no custom-fields)
                    foreach ($postType->getFieldGroups() as $fieldGroup) {
                        $fieldGroup->setPostType($postType);

                        //  get first field (unless field list is empty)
                        $fields = $fieldGroup->getFields();
                        $firstField = reset($fields);

                        //  if at least this one field is defined
                        if (!empty($firstField)) {
                            //  use its ID and stop the loop
                            $firstFieldId = $fieldGroup->getFieldId($firstField);
                            break;
                        }
                    }

                    //  get post ID, use random hash as fallback
                    $fallback = empty($postAttr['ID']) ? substr(md5(microtime(true)), 0, 10) : $postAttr['ID'];
                    //  get the value of the first field, fallback is the ID (or the hash)
                    $title = empty($firstFieldId) || empty($postAttr[$firstFieldId]) ? $fallback : $postAttr[$firstFieldId];
                    //  get slug using the title
                    $slug = sanitize_title($title, $fallback);

                    //  apply the post's new title and slug
                    $data['post_title'] = $title;
                    $data['post_name'] = $slug;
                }
            }
        }

        return $data;
    }

    /**
     * Adds custom fields to the list of fields which have to be saved using revisions. Depends on the post-types'
     * revision support.
     *
     * @param array $fields list of fields to revision, contains 'post_title', 'post_content', and 'post_excerpt' by
     *     default.
     * @param array $post a post array being processed for insertion as a post revision
     *
     * @return array the filtered list including all added custom-fields
     *
     * @see https://developer.wordpress.org/reference/hooks/_wp_post_revision_fields/
     * @see \Vierbeuter\WordPress\Feature\CustomPostType\CustomPostType::supportsRevisions()
     */
    public function filterRevisionFields(array $fields, array $post)
    {
        //  iterate all post-types to add their fields to $fields array
        foreach ($this->getPostTypes() as $postType) {

            //  only those fields are of interest whose overlying post-types support revisions
            if ($postType->supportsRevisions()) {

                foreach ($postType->getFieldGroups() as $fieldGroup) {
                    $fieldGroup->setPostType($postType);

                    foreach ($fieldGroup->getFields() as $field) {
                        $fieldKey = $fieldGroup->getFieldDbMetaKey($field);
                        $fieldLabel = $fieldGroup->getLabel() . ' > ' . $field->getLabel();
                        $fields[$fieldKey] = $fieldLabel;
                    }
                }
            }
        }

        return $fields;
    }
}
