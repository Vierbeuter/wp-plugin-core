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
        foreach ($this->initPostTypes() as $postType) {
            //  pass translator to post-type (for translating labels and buttons and such stuff)
            $postType->setTranslator($this->getTranslator());
            //  activate the post-type
            $postType->activate();
            //  keep in mind the current post-type using its slug as key
            //  post-types will be accessed later, see WordPress hooks
            $this->postTypes[$postType->getSlug()] = $postType;
        }

        parent::activate();
    }

    /**
     * Returns the list of post-types to be registered.
     *
     * @return CustomPostType[]
     */
    abstract protected function initPostTypes(): array;

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
            'manage_posts_custom_column',        //  for non hierarchical post-types
            /** @see \Vierbeuter\WordPress\Feature\AddCustomPostTypes::manage_pages_custom_column() */
            'manage_pages_custom_column' => [    //  for hierarchical/nestable post-types
                'args' => 2,
            ],
            /** @see \Vierbeuter\WordPress\Feature\AddCustomPostTypes::admin_head() */
            'admin_head',
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
            'pre_get_posts',
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
                //  register field-group to current post-type to render its form-fields
                $fieldGroup->register($postType);
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
        $postType = $this->getPostType($postTypeSlug);

        //  only if post-type found
        if (!empty($postType)) {
            //  iterate the post-type's field-groups
            foreach ($postType->getFieldGroups() as $fieldGroup) {
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
            echo sprintf(__('The column <code>%1$s</code> is unknown, no value to be rendered. Please implement <code>%2$s</code> method to determine a string-representation of the column value.',
                    VBC_LANGUAGES_DOMAIN), $column, get_class($postType) . '->' . $getMethod . '()') . '<br />';
            echo __('In case of a standard custom-field the implementation may look like the following:',
                VBC_LANGUAGES_DOMAIN);
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
            echo sprintf(__('You can also use the <code>%1$s</code>-methods to render field values that are non-standard respectively of a specific type or kind such as the title and an image URL.',
                    VBC_LANGUAGES_DOMAIN), 'getColumnValueFor…') . '<br />';
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
     * Hooks into the same-named action hook to extend the fulltext-search for also searching custom-fields of a
     * post-type.
     *
     * @param \WP_Query $query
     *
     * @see http://wordpress.stackexchange.com/questions/11758/extending-the-search-context-in-the-admin-list-post-screen
     */
    public function pre_get_posts(\WP_Query $query): void
    {
        //  only change database-queries that are made for a search in the admin panel
        if ($query->is_search() && $query->is_admin) {
            //  determine post-type
            $postType = $this->getPostType($query->query['post_type']);

            //  if no post-type found then let the given query untouched
            //  ignore any custom-fields for now and therefore don't extend the fulltext-search at all  …
            if (empty($postType)) {
                return;
            }

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
}
