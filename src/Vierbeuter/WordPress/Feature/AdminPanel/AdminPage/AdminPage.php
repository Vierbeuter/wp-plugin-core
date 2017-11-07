<?php

namespace Vierbeuter\WordPress\Feature\AdminPanel\AdminPage;

use Vierbeuter\WordPress\Di\Component;
use Vierbeuter\WordPress\Service\Translator;

/**
 * The AdminPage class can be extended to implement pages to be included in the WP admin panel.
 *
 * @package Lenspire\WordPress\Feature\AdminPage
 */
abstract class AdminPage extends Component
{

    /**
     * @var \Vierbeuter\WordPress\Service\Translator
     */
    protected $translator;

    /**
     * AdminPage constructor.
     *
     * @param \Vierbeuter\WordPress\Service\Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Returns a translation for the given text.
     *
     * @param string $text
     *
     * @return string
     */
    protected function translate(string $text): string
    {
        return $this->translator->translate($text);
    }

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
        //	first of all handle the POST object unless empty
        if (!empty($_POST)) {
            $this->handlePost($_POST);
        }

        //	render the page template
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
