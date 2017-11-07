<?php

namespace Vierbeuter\WordPress\Feature\AdminPanel\SidebarMenu;

use Vierbeuter\WordPress\Di\Component;
use Vierbeuter\WordPress\Service\Translator;

/**
 * The SidebarMenu class can be extended to implement sidebar menus to be added to the WP admin panel.
 *
 * @package Lenspire\WordPress\Feature\AdminPanel
 */
abstract class SidebarMenu extends Component
{

    /**
     * @var \Vierbeuter\WordPress\Service\Translator
     */
    private $translator;

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
     * Returns the page title for this menu.
     *
     * @return string
     */
    abstract public function getPageTitle(): string;

    /**
     * Returns the menu title as added to admin sidebar.
     *
     * @return string
     */
    abstract public function getMenuTitle(): string;

    /**
     * Returns the capability required for this menu to be displayed to the user.
     *
     * @return string
     *
     * @see https://codex.wordpress.org/Roles_and_Capabilities
     */
    public function getCapability(): string
    {
        return 'manage_options';
    }

    /**
     * Returns the icon URL or the name of the WP dashicon.
     *
     * @return string
     *
     * @see https://developer.wordpress.org/resource/dashicons/
     */
    abstract public function getIcon(): string;

    /**
     * Returns the
     *
     * @return int
     *
     * @see https://developer.wordpress.org/reference/functions/add_menu_page/#menu-structure
     */
    public function getPosition(): int
    {
        return 30;
    }

    /**
     * Returns a list of admin-panel pages to be added to the WP admin-panel's sidebar.
     *
     * The first item of the returned array is gonna be used as main page and therefore it will be added as menu entry
     * to the sidebar while all other items are its children and added as submenu entries.
     *
     * @return \Vierbeuter\WordPress\Feature\AdminPanel\AdminPage\AdminPage[]
     */
    abstract public function getAdminPanelPages(): array;
}
