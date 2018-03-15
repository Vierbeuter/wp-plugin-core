# Change Log

## [0.10.0](https://github.com/Vierbeuter/wp-plugin-core/tree/0.10.0) (2018-03-15)
### Added
 * added revision support for post-types and their custom-fields
 
### Fixed
 * fixed the default columns of custom post-types in the admin panel's list-view of WP (title and date) to make them sortable
 * several fixes of docs

## [0.9.5](https://github.com/Vierbeuter/wp-plugin-core/tree/0.9.5) (2018-01-10)
### Fixed
 * minor bugfix in `WpmlWpOptions->getByPageClass(…)` method which used the active language (as determined by WPML) instead of the one passed to it

## [0.9.4](https://github.com/Vierbeuter/wp-plugin-core/tree/0.9.4) (2018-01-10)
### Added
 * made it possible to pass route args to REST API endpoints

## [0.9.3](https://github.com/Vierbeuter/wp-plugin-core/tree/0.9.3) (2018-01-09)
### Added
 * added `WpmlWpOptionsPage` to make better use of `WpmlWpOptions` service

### Changed
 * remove requirement of WPML's String Translation module (as added in 0.9.2)
 * config pages for `wp_options` (if extending `WpmlWpOptionsPage`) can now take care of WPML's language switch located in WP's admin bar (config pages that don't have to be translatable can still extend `WpOptionsPage`)

## ~~[0.9.2](https://github.com/Vierbeuter/wp-plugin-core/tree/0.9.2) (2018-01-09)~~
### ~~Added~~
 * ~~added new `WpmlWpOptions` service supporting [WPML](https://wpml.org/) for making `wp_options` translatable (requires the [String Translation module](https://wpml.org/documentation/getting-started-guide/string-translation/))~~

*Note*: Changes have been nearly completely overriden by 0.9.3 Do not rely on this version, update ASAP.

## [0.9.1](https://github.com/Vierbeuter/wp-plugin-core/tree/0.9.1) (2017-11-30)
### Added
 * added `WpEditor` field to use `wp_editor(…)`

## [0.9.0](https://github.com/Vierbeuter/wp-plugin-core/tree/0.9.0) (2017-11-29)
### Added
 * added base classes to easily implement settings pages in WP admin panel using `wp_options`
 * added a ready-to-use service class for accessing `wp_options`
 * added more detailed error message on registering DI-components with invalid parameter signature (&rarr; when passing parameters to `Container->addComponent(…)` that do not match the parameter list of the component's constructor)

## [0.8.1](https://github.com/Vierbeuter/wp-plugin-core/tree/0.8.1) (2017-11-21)
### Fixed
 * fixed a dumb copy'n'paste error in one of the recently added features (the one for removing default REST API endpoints as added by WordPress)

## [0.8.0](https://github.com/Vierbeuter/wp-plugin-core/tree/0.8.0) (2017-11-21)
### Added
 * added base classes to easily implement new API endpoints and to add them to the WP REST API

## [0.7.0](https://github.com/Vierbeuter/wp-plugin-core/tree/0.7.0) (2017-11-15)
### Added
 * added base classes to easily implement new pages for WP admin panel as well as to add menu entries to the sidebar
 * added some missing translations

###  Fixed
 * some (really) minor fixes and code style changes

## [0.6.1](https://github.com/Vierbeuter/wp-plugin-core/tree/0.6.1) (2017-10-16)
### Fixed
 * fixed bug for post-types' and taxonomies' WP hook support

## [0.6.0](https://github.com/Vierbeuter/wp-plugin-core/tree/0.6.0) (2017-10-16)
### Added
 * WP hook support for post-types, taxonomies and custom fields
 * custom fields got a default implementation of the new `enqueueScripts()` method (hooking into `admin_enqueue_scripts`) &rarr; the method can be overridden if a field needs additional scripts to be loaded in wp-admin panel (e.g. jQuery UI elements)

## [0.5.0](https://github.com/Vierbeuter/wp-plugin-core/tree/0.5.0) (2017-10-13)
### Changed
 * refactorings of post-types and taxonomies and a few changes of feature base class for improving DI-integration

## [0.4.0](https://github.com/Vierbeuter/wp-plugin-core/tree/0.4.0) (2017-10-12)
### Added
 * developer guide for implementing a plugin &rarr; see [HOW-TO](./doc/HOW-TO.md)

### Changed
 * plugin makes now more use of dependency injection to better handle features and other components a plugin is dependant on
 * therefore improved a plugin's bootstrapping process (due to better DI-integration)

## [0.3.0](https://github.com/Vierbeuter/wp-plugin-core/tree/0.3.0) (2017-10-09)
### Added
 * custom-field support for taxonomies (similar to custom-fields for post-types) 

## [0.2.2](https://github.com/Vierbeuter/wp-plugin-core/tree/0.2.2) (2017-10-04)
### Fixed
 * up to now autoloading and plugin activation only worked for a single plugin, from now on multiple plugins can be regsitered and activated

## [0.2.0](https://github.com/Vierbeuter/wp-plugin-core/tree/0.2.0) (2017-09-18)
### Added
 * made the whole thing translatable (`Translator` component has been existing but without actual functionality)
 * also added differentiation between core translations and plugin translations
 * added German translations for some texts of base classes

## [0.1.0](https://github.com/Vierbeuter/wp-plugin-core/tree/0.1.0) (2017-09-14)
First version of WP plugin core released. This one contains:
 * base architecture for implementing onw plugins with own features
 * base classes for easily implementing and adding custom post-types and even custom-fields
 * base classes for easily implementing and adding custom taxonomies
 * base class for easily adding new image sizes
