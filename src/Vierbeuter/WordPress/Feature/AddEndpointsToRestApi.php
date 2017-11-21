<?php

namespace Vierbeuter\WordPress\Feature;

use Vierbeuter\WordPress\Feature\ApiEndpoint\ApiEndpoint;

/**
 * The AddEndpointsToRestApi feature defines all REST API endpoints.
 *
 * @package Lenspire\WordPress\Feature
 *
 * @see https://developer.wordpress.org/reference/hooks/rest_api_init/
 * @see https://developer.wordpress.org/rest-api/extending-the-rest-api/
 */
abstract class AddEndpointsToRestApi extends Feature
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
            /** @see \Vierbeuter\WordPress\Feature\AddEndpointsToRestApi::initApiEndpoints() */
            'rest_api_init' => 'initApiEndpoints',
        ];
    }

    /**
     * Adds endpoints to the WP REST API using the <code>addApiEndpoint(…)</code> method.
     *
     * @see \Vierbeuter\WordPress\Feature\AddEndpointsToRestApi::addApiEndpoint()
     */
    abstract public function initApiEndpoints(): void;

    /**
     * Adds the given endpoint to the REST API.
     *
     * This method can be used from within the <code>initApiEndpoints()</code> method.
     *
     * @param string $apiEndpointClass the API endpoint's class name to be added, the class has to be a sub-class of
     *     ApiEndpoint
     * @param array $paramNames names of parameters to be passed to the API endpoint's constructor, the parameters are
     *     expected to be found in the DI-containter as well, ensure they are added before accessing the given API
     *     endpoint
     *
     * @throws \Exception
     *
     * @see \Vierbeuter\WordPress\Feature\AddEndpointsToRestApi::initApiEndpoints()
     * @see \Vierbeuter\WordPress\Feature\ApiEndpoint\ApiEndpoint
     */
    protected function addApiEndpoint(string $apiEndpointClass, ...$paramNames): void
    {
        //  check API endpoint class first
        if (empty($apiEndpointClass) || !is_subclass_of($apiEndpointClass, ApiEndpoint::class)) {
            throw new \InvalidArgumentException('Given class "' . $apiEndpointClass . '" needs to be a valid sub-class of "' . ApiEndpoint::class . '"');
        }

        //  add API endpoint only once
        if (empty($this->getComponent($apiEndpointClass))) {
            //  add to DI-container
            $this->addComponent($apiEndpointClass, ...$paramNames);
            //  instantiate by getting the API endpoint from container
            /** @var \Vierbeuter\WordPress\Feature\ApiEndpoint\ApiEndpoint $apiEndpoint */
            $apiEndpoint = $this->getComponent($apiEndpointClass);

            //  register API endpoint
            $apiEndpoint->register();
        }
    }
}
