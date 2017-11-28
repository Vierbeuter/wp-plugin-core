<?php

namespace Vierbeuter\WordPress\Feature\AdminPanel\AdminPage;

use Vierbeuter\WordPress\Di\Component;
use Vierbeuter\WordPress\Feature\Traits\HasWpHookSupport;
use Vierbeuter\WordPress\Traits\HasTranslatorSupport;

/**
 * The AdminPage class can be extended to implement pages to be included in the WP admin panel.
 *
 * @package Vierbeuter\WordPress\Feature\AdminPanel\AdminPage
 */
abstract class AdminPage extends Component
{

    /**
     * include methods for translating texts
     */
    use HasTranslatorSupport;
    /**
     * include methods for being able to provide WP-hook implementations
     */
    use HasWpHookSupport;

    /**
     * Returns the page's title as set in HTML head.
     *
     * @return string
     */
    abstract public function getPageTitle(): string;

    /**
     * Returns the page's submenu label as added to admin sidebar.
     *
     * @return string
     */
    abstract public function getSubmenuTitle(): string;

    /**
     * Returns the page's menu slug (which is required to be a unique identifier).
     *
     * @return string
     */
    abstract public function getSlug(): string;

    /**
     * Renders the page's content.
     */
    public function handlePostAndRenderPage(): void
    {
        //  first of all handle the POST object unless empty
        if (!empty($_POST)) {
            $this->handlePost($_POST);
        }

        //  render the page template
        $this->render();
    }

    /**
     * Handles post requests.
     *
     * @param array $post
     */
    abstract protected function handlePost(array $post): void;

    /**
     * Renders the page.
     */
    abstract protected function render(): void;
}
