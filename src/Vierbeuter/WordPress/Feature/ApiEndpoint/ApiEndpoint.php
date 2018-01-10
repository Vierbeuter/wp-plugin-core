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
     * Routes are expected to be strings. Optionally an args array can be given as array value while the array key is
     * the route.
     *
     * Therefore, the returned value may be a simple (indexed) list of routes, an associative array (respectively a map
     * or dictionary or whatever you want to call it) of routes and their args or it can be a mixture of simple values
     * and key-value-pairs.
     *
     * Sample:
     *
     * <code>
     * return [
     *     'my-awesome-route',
     *     'route-with-args/(?P<myarg>\d+)' => [
     *         'myarg' => [
     *             // …
     *         ]
     *         // add args here …
     *     ],
     *     'my-other/awesome-route',
     * ];
     * </code>
     *
     * See official WP docs of register_rest_route(…) function where you'll find examples for routes.
     *
     * @return string[]
     *
     * @see https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/#arguments
     * @see https://developer.wordpress.org/reference/functions/register_rest_route/
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
     * @see https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
     */
    public function register(): void
    {
        foreach ($this->getRoutes() as $route => $routeArgs) {
            //  if current route has no args given (since it's an indexed array entry)
            if (is_int($route)) {
                //  make it a key-value-pair of route and its (empty) args
                $route = $routeArgs;
                $routeArgs = [];
            }

            //  check the route's data type
            if (!is_string($route)) {
                throw new \InvalidArgumentException('The given route is expected to be a string but ' . gettype($route) . ' given: "' . $route . '"');
            }
            //  check the route args' data type
            if (!is_array($routeArgs)) {
                throw new \InvalidArgumentException('The given route args are expected to be an array but ' . gettype($routeArgs) . ' given: "' . $routeArgs . '"');
            }

            //  register the route
            register_rest_route($this->getNamespace(), $route, [
                'methods' => $this->getMethod(),
                'callback' => [$this, 'getResponseData'],
                'args' => $routeArgs,
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
