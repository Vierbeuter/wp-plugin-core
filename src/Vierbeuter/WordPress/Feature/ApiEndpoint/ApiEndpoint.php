<?php

namespace Vierbeuter\WordPress\Feature\ApiEndpoint;

use Vierbeuter\WordPress\Di\Component;

/**
 * The ApiEndpoint is a base class for easily implementing API endpoints, optionally having even multple routes.
 *
 * @package Vierbeuter\WordPress\Feature\SingleApiEndpoint
 */
abstract class ApiEndpoint extends Component
{

    /**
     * Returns the endpoint's namespace (more or less the route prefix).
     *
     * @return string
     */
    abstract public function getNamespace(): string;

    /**
     * Returns the endpoint's routes.
     *
     * @return string[]
     */
    abstract public function getRoutes(): array;

    /**
     * Returns the endpoint's method which should be one of the constants of \WP_REST_Server class (which is in the end
     * "GET", "POST" etc.).
     *
     * @return string
     *
     * @see \WP_REST_Server::READABLE
     * @see \WP_REST_Server::CREATABLE
     * @see \WP_REST_Server::EDITABLE
     * @see \WP_REST_Server::DELETABLE
     * @see \WP_REST_Server::ALLMETHODS
     */
    public function getMethod(): string
    {
        return \WP_REST_Server::READABLE;
    }

    /**
     * Registers all routes to match this endpoint implementation.
     *
     * @see https://developer.wordpress.org/reference/functions/register_rest_route/
     */
    public function register(): void
    {
        foreach ($this->getRoutes() as $route) {
            //  check the route's data type
            if (!is_string($route)) {
                throw new \InvalidArgumentException('The given route is expected to be a string but ' . gettype($route) . ' given: "' . $route . '"');
            }

            //  register the route
            register_rest_route($this->getNamespace(), $route, [
                'methods' => $this->getMethod(),
                'callback' => [$this, 'getResponseData'],
            ]);
        }
    }

    /**
     * Creates the API response for given request.
     *
     * @param \WP_REST_Request $request
     *
     * @return mixed
     */
    abstract public function getResponseData(\WP_REST_Request $request);

    /**
     * Returns the request parameter for given name or the given default value (respectively NULL if none given).
     *
     * @param \WP_REST_Request $request
     * @param string $name
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    protected function getParam(\WP_REST_Request $request, string $name, $default = null)
    {
        return empty($request[$name]) ? $default : $request[$name];
    }
}
