# Change Log

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
