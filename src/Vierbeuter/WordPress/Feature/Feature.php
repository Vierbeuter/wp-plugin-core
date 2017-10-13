<?php

namespace Vierbeuter\WordPress\Feature;

use Vierbeuter\WordPress\Di\Component;
use Vierbeuter\WordPress\Feature\Traits\HasAdminNoticeSupport;
use Vierbeuter\WordPress\Feature\Traits\HasWpHookSupport;
use Vierbeuter\WordPress\Traits\HasTranslatorSupport;

/**
 * A Feature implementation extends the standard WordPress functionality.
 *
 * @package Vierbeuter\WordPress\Feature
 */
abstract class Feature extends Component
{

    /**
     * include methods for hooking into WP
     */
    use HasWpHookSupport;
    /**
     * include methods for translating strings
     */
    use HasTranslatorSupport;
    /**
     * include methods for adding system mesages in admin-panel
     */
    use HasAdminNoticeSupport;

    /**
     * Activates the feature to actually extend WP functionality.
     */
    public function activate(): void
    {
        $this->initWpHooks();
    }
}
