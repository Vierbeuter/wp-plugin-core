<?php

namespace Vierbeuter\WordPress\Feature;

/**
 * The RemoveDefaultApiEndpoints feature removes the default REST API endpoints as defined by WordPress.
 *
 * @package Vierbeuter\WordPress\Feature
 */
abstract class RemoveDefaultApiEndpoints extends Feature
{

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
            /** @see \Vierbeuter\WordPress\Feature\RemoveDefaultApiEndpoints::rest_api_init() */
            'rest_api_init',
        ];
    }

    /**
     * Removes all WP default endpoints.
     *
     * @see https://developer.wordpress.org/reference/hooks/rest_api_init/
     * @see https://developer.wordpress.org/reference/functions/create_initial_rest_routes/
     */
    public function rest_api_init(): void
    {
        //  suppress adding default endpoints (such as "/wp/v2/posts", "/wp/v2/categories" etc.)
        remove_action('rest_api_init', 'create_initial_rest_routes', 99);
    }
}
