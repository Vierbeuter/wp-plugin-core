# Development roadmap

*Todos and features for future releases.*

## 1.0.0

 * ~~feature for adding custom taxonomies~~ (added to 0.1.0)
 * ~~add [translations](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/) for core~~ (added to 0.2.0)
 * ~~ensure a plugin dev is able to translate his/her plugin as well~~ (added to 0.2.0)
 * ~~docs! … add a (simple) how-to or guide~~(added to 0.4.0)
 * some last optimizations and todos for custom taxonomies and fields
 * maybe add some additional features or base classes for feature implementations … maybe

## 1.1.0

 * api feature for easily creating API endpoints using the [WP REST API](https://developer.wordpress.org/rest-api/)
 * database service for easily [querying WP posts](https://codex.wordpress.org/Class_Reference/WP_Query)

---

*Todos and features that might find their way into the lib sooner or later.*

## 1.x.0

 * preparations (abstract classes) or even implementations (concrete classes for direct use) of some more features such as
   * EnableSvgUpload
   * EnableFilenameSearchInMediaLibrary
   * …
 * import/export feature for posts of custom post-types
 * support of several filetypes for import/export
   * JSON
   * CSV
   * YAML (optional?)
   * XLSX (optional?)
 * TeX service for creating PDFs (or at least a set of classes to simplify the implementation of an actual service that creates PDFs using [La]TeX)
 * shortcode service (respectively a set of classes) to "misuse" shortcodes for creating configuration objects instead of strings
