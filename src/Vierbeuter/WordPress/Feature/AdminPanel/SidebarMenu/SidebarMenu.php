<?php

namespace Vierbeuter\WordPress\Feature\AdminPanel\SidebarMenu;

use Vierbeuter\WordPress\Di\Component;
use Vierbeuter\WordPress\Feature\AdminPanel\AdminPage\AdminPage;
use Vierbeuter\WordPress\Traits\HasTranslatorSupport;

/**
 * The SidebarMenu class can be extended to implement sidebar menus to be added to the WP admin panel.
 *
 * @package Vierbeuter\WordPress\Feature\AdminPanel\SidebarMenu
 */
abstract class SidebarMenu extends Component
{

    /**
     * include methods for translating texts
     */
    use HasTranslatorSupport;

    /**
     * @var \Vierbeuter\WordPress\Feature\AdminPanel\AdminPage\AdminPage[]
     */
    protected $adminPages;

    /**
     * SidebarMenu constructor.
     */
    public function __construct()
    {
        $this->adminPages = [];
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
     * Returns a list of admin-panel pages to be added to this menu which is placed in the WP admin-panel's sidebar.
     *
     * The first item of the returned array is gonna be used as main page and therefore it will be added as menu entry
     * to the sidebar while all other items are its children and added as submenu entries.
     * See also the hook implementation of <code>admin_menu</code> in <code>AddPagesToAdminPanel</code> class.
     *
     * @return \Vierbeuter\WordPress\Feature\AdminPanel\AdminPage\AdminPage[]
     *
     * @see \Vierbeuter\WordPress\Feature\AddPagesToAdminPanel::admin_menu()
     */
    public function getAdminPanelPages(): array
    {
        return $this->adminPages;
    }

    /**
     * Initializes the menu, adds admin-panel pages using the <code>addAdminPage(…)</code> method.
     *
     * @see \Vierbeuter\WordPress\Feature\AdminPanel\SidebarMenu\SidebarMenu::addAdminPage()
     */
    abstract public function initAdminPanelPages(): void;

    /**
     * Adds an admin-panel page for given classname to this sidebar menu.
     *
     * This method can be used from within the <code>initAdminPanelPages()</code> method.
     *
     * @param string $adminPageClass the page's class name to be added, the class has to be a sub-class of AdminPage
     * @param array $paramNames names of parameters to be passed to the sidebar menu's constructor, the parameters are
     *     expected to be found in the DI-containter as well, ensure they are added before accessing the given admin
     *     page
     *
     * @see \Vierbeuter\WordPress\Feature\AdminPanel\SidebarMenu\SidebarMenu::initAdminPanelPages()
     * @see \Vierbeuter\WordPress\Feature\AdminPanel\AdminPage\AdminPage
     */
    protected function addAdminPage(string $adminPageClass, ...$paramNames): void
    {
        //  check sidebar menu class first
        if (empty($adminPageClass) || !is_subclass_of($adminPageClass, AdminPage::class)) {
            throw new \InvalidArgumentException('Given class "' . $adminPageClass . '" needs to be a valid sub-class of "' . AdminPage::class . '"');
        }

        //  get the admin page from container
        /** @var \Vierbeuter\WordPress\Feature\AdminPanel\AdminPage\AdminPage $adminPage */
        $adminPage = $this->getComponent($adminPageClass);

        //  check if empty to add admin page only once
        if (empty($adminPage)) {
            //  add to DI-container
            $this->addComponent($adminPageClass, ...$paramNames);
            //  instantiate by getting the admin page from container
            $adminPage = $this->getComponent($adminPageClass);
        }

        //  add to list of registered pages
        $this->adminPages[] = $adminPage;
    }
}
