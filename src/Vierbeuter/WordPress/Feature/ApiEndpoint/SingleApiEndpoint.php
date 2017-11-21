<?php

namespace Vierbeuter\WordPress\Feature\ApiEndpoint;

/**
 * The SingleApiEndpoint is a base class for easily implementing API endpoints having only a single route.
 *
 * @package Vierbeuter\WordPress\Feature\SingleApiEndpoint
 */
abstract class SingleApiEndpoint extends ApiEndpoint
{

    /**
     * Returns the endpoint's routes.
     *
     * @return string[]
     */
    public function getRoutes(): array
    {
        return [$this->getRoute()];
    }

    /**
     * Returns the endpoint's route.
     *
     * @return string
     */
    abstract public function getRoute(): string;
}
