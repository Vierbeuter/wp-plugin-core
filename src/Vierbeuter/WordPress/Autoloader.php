<?php

namespace Vierbeuter\WordPress;

/**
 * The Autoloader class provides an autoloading mechanism for a plugin's PHP classes.
 *
 * @package Vierbeuter\WordPress
 */
class Autoloader
{

    /**
     * @var \Vierbeuter\WordPress\Autoloader
     */
    private static $autoloader;

    /**
     * @var string[]
     */
    private $sourcePaths;

    /**
     * Registers an autoloader for the current plugin.
     *
     * Example usage within "your-awesome-plugin/index.php" (before activating your plugin):
     * <code>
     * \Vierbeuter\WordPress\Autoloader::register(__FILE__);
     * </code>
     *
     * @param string $pluginFile absolute path of the WordPress plugin's index.php file.
     * @param string $sourceDir (optional) directory name containing all plugin sources (the PHP classes), default is
     *     "src"
     *
     * @see \Vierbeuter\WordPress\Plugin::activate()
     */
    public static function register(string $pluginFile, string $sourceDir = 'src'): void
    {
        /**
         * register autoload method
         */

        //  create autoloader only once
        if (empty(self::$autoloader)) {
            //  create autoloader
            self::$autoloader = new static();
            self::$autoloader->sourcePaths = [];

            //  register the autoload method
            spl_autoload_register([self::$autoloader, 'autoload']);
        }

        /**
         * register plugin files
         */

        //  determine base path, add trailing slash
        $pluginPath = rtrim(dirname($pluginFile), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        //  also ensure to have trailing slash for given source directory
        $sourceDir = trim($sourceDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        //  add path to plugin sources
        self::$autoloader->sourcePaths[$pluginFile] = $pluginPath . $sourceDir;
    }

    /**
     * Autoloads the given class.
     *
     * @param string $className
     */
    public function autoload(string $className): void
    {
        //  get file path for the given class (namespace corresponds to directory structure)
        $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';

        //  try all registered source paths
        foreach ($this->sourcePaths as $sourcePath) {
            $filePath = $sourcePath . $fileName;

            //  include file for class unless it doesn't exist
            if (file_exists($filePath)) {
                require_once $filePath;

                //  no other source paths to check
                return;
            }
        }
    }
}
