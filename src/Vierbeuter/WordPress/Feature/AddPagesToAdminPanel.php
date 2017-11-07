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
 */
abstract class AddPagesToAdminPanel extends Feature
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
            'admin_menu',
        ];
    }

    /**
     * Hooks into "admin_menu" to add menu entries to the admin-panel's sidebar as returned by getAdminPanelPages()
     * method.
     *
     * @see \Vierbeuter\WordPress\Feature\AddPagesToAdminPanel::getAdminPanelPages()
     * @see https://developer.wordpress.org/reference/functions/add_menu_page/
     * @see https://developer.wordpress.org/reference/functions/add_submenu_page/
     */
    public function admin_menu()
    {
        /** @var \Vierbeuter\WordPress\Feature\AdminPanel\SidebarMenu\SidebarMenu $menus */
        $menus = $this->getSidebarMenus();

        foreach ($menus as $menu) {
            //  first of all check the current menu
            if (!$menu instanceof SidebarMenu) {
                throw new \Exception('Given $menu (which is an instance of "' . get_class($menu) . '") is expected to be a sub-class of "' . SidebarMenu::class . '" but isn\'t.');
            }

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
     * Returns a list of menu entries to be added to the WP admin-panel's sidebar.
     *
     * @return \Vierbeuter\WordPress\Feature\AdminPanel\SidebarMenu\SidebarMenu[]
     */
    abstract protected function getSidebarMenus(): array;
}
