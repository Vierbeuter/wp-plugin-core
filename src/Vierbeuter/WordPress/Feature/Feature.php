<?php

namespace Vierbeuter\WordPress\Feature;

use Vierbeuter\WordPress\Feature\Traits\HasWpHookSupport;

/**
 * A Feature implementation extends the standard WordPress functionality.
 *
 * @package Vierbeuter\WordPress\Feature
 */
abstract class Feature
{

    /**
     * include methods for hooking into WP
     */
    use HasWpHookSupport;

    /**
     * Activates the feature to actually extend WP functionality.
     */
    public function activate(): void
    {
        $this->initWpHooks();
    }
}
