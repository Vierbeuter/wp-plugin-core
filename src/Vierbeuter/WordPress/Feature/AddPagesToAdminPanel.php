<?php

namespace Vierbeuter\WordPress\Feature;

use Vierbeuter\WordPress\Feature\AdminPanel\AdminPage\AdminPage;
use Vierbeuter\WordPress\Feature\AdminPanel\SidebarMenu\SidebarMenu;

/**
 * The AddPagesToAdminPanel feature adds pages to WordPress' admin-panel as well as corresponding menu entries to the
 * sidebar.
 *
 * @package Vierbeuter\WordPress\Feature
 *
 * @see \Vierbeuter\WordPress\Feature\AdminPanel\AdminPage\AdminPage
 * @see \Vierbeuter\WordPress\Feature\AdminPanel\SidebarMenu\SidebarMenu
 */
abstract class AddPagesToAdminPanel extends Feature
{

    /**
     * @var \Vierbeuter\WordPress\Feature\AdminPanel\SidebarMenu\SidebarMenu[]
     */
    protected $sidebarMenus;

    /**
     * AddPagesToAdminPanel constructor.
     */
    public function __construct()
    {
        $this->sidebarMenus = [];
    }

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
            'admin_menu',
        ];
    }

    /**
     * Hooks into "admin_menu" to add menu entries to the admin-panel's sidebar as defined by
     * <code>initSidebarMenus()</code> method.
     *
     * @see \Vierbeuter\WordPress\Feature\AddPagesToAdminPanel::initSidebarMenus()
     * @see https://developer.wordpress.org/reference/functions/add_menu_page/
     * @see https://developer.wordpress.org/reference/functions/add_submenu_page/
     */
    public function admin_menu(): void
    {
        //  initialize the sidebar menus
        $this->initSidebarMenus();

        //  itereate the menus and add one by one to the sidebar
        foreach ($this->sidebarMenus as $menu) {
            //  first of all check the current menu
            if (!$menu instanceof SidebarMenu) {
                throw new \Exception('Given $menu (which is an instance of "' . get_class($menu) . '") is expected to be a sub-class of "' . SidebarMenu::class . '" but isn\'t.');
            }

            //  initialize the admin pages for the current menu
            $menu->initAdminPanelPages();
            //  get all pages for this menu
            /** @var \Vierbeuter\WordPress\Feature\AdminPanel\AdminPage\AdminPage[] $pages */
            $pages = $menu->getAdminPanelPages();

            //  check list of pages to be added to the sidebar
            if (empty($pages)) {
                throw new \Exception('Given pages array may not be empty. At least one page expected.');
            }

            //  get the main page which is simply the array's first item
            /** @var \Vierbeuter\WordPress\Feature\AdminPanel\AdminPage\AdminPage $firstPage */
            $firstPage = reset($pages);

            //  check the first page
            if (!$firstPage instanceof AdminPage) {
                throw new \Exception('Given $firstPage (which is an instance of "' . get_class($firstPage) . '") is expected to be a sub-class of "' . AdminPage::class . '" but isn\'t.');
            }

            //  add the menu to the admin panel's sidebar
            add_menu_page(
                $menu->getPageTitle(),
                $menu->getMenuTitle(),
                $menu->getCapability(),
                $firstPage->getSlug(),
                [$firstPage, 'handlePostAndRenderPage'],
                $menu->getIcon(),
                $menu->getPosition()
            );

            //  add submenus to the menu added above
            foreach ($pages as $page) {
                //  check the current page
                if (!$page instanceof AdminPage) {
                    throw new \Exception('Given $page (which is an instance of "' . get_class($page) . '") is expected to be a sub-class of "' . AdminPage::class . '" but isn\'t.');
                }

                //  add the submenu as child of above menu to the admin panel's sidebar
                add_submenu_page(
                    $firstPage->getSlug(),
                    $page->getPageTitle(),
                    $page->getSubmenuTitle(),
                    $menu->getCapability(),
                    $page->getSlug(),
                    [$page, 'handlePostAndRenderPage']
                );
            }
        }
    }

    /**
     * Initializes the feature, adds sidebar menus using the <code>addSidebarMenu(…)</code> method.
     *
     * @see \Vierbeuter\WordPress\Feature\AddPagesToAdminPanel::addSidebarMenu()
     */
    abstract protected function initSidebarMenus(): void;

    /**
     * Adds a menu entry for given classname to be added to the sidebar of WP's admin-panel.
     *
     * This method can be used from within the <code>initSidebarMenus()</code> method.
     *
     * @param string $sidebarMenuClass the sidebar menu's class name to be added, the class has to be a sub-class of
     *     SidebarMenu
     * @param array $paramNames names of parameters to be passed to the sidebar menu's constructor, the parameters are
     *     expected to be found in the DI-containter as well, ensure they are added before accessing the given sidebar
     *     menu
     *
     * @see \Vierbeuter\WordPress\Feature\AddPagesToAdminPanel::initSidebarMenus()
     * @see \Vierbeuter\WordPress\Feature\AdminPanel\SidebarMenu\SidebarMenu
     */
    protected function addSidebarMenu(string $sidebarMenuClass, ...$paramNames): void
    {
        //  check sidebar menu class first
        if (empty($sidebarMenuClass) || !is_subclass_of($sidebarMenuClass, SidebarMenu::class)) {
            throw new \InvalidArgumentException('Given class "' . $sidebarMenuClass . '" needs to be a valid sub-class of "' . SidebarMenu::class . '"');
        }

        //  get the sidebar menu from container
        /** @var \Vierbeuter\WordPress\Feature\AdminPanel\SidebarMenu\SidebarMenu $sidebarMenu */
        $sidebarMenu = $this->getComponent($sidebarMenuClass);

        //  check if empty to add sidebar menu only once
        if (empty($sidebarMenu)) {
            //  add to DI-container
            $this->addComponent($sidebarMenuClass, ...$paramNames);
            //  instantiate by getting the sidebar menu from container
            $sidebarMenu = $this->getComponent($sidebarMenuClass);
        }

        //  add to list of registered menus
        $this->sidebarMenus[] = $sidebarMenu;
    }
}
