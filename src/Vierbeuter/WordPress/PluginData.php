<?php

namespace Vierbeuter\WordPress;

use Vierbeuter\WordPress\Di\Component;

/**
 * The PluginData class provides properties and methods describing the plugin.
 *
 * @package Vierbeuter\WordPress\Traits
 */
class PluginData extends Component
{

    /**
     * complete server path to the plugin file
     *
     * e.g. "/var/www/your-domain.com/…/your-awesome-plugin/index.php"
     *
     * @var string
     */
    protected $pluginFile;

    /**
     * complete server path to the plugin including trailing slash
     *
     * e.g. "/var/www/your-domain.com/…/your-awesome-plugin/"
     *
     * @var string
     */
    protected $pluginDir;

    /**
     * complete (web) URL to the plugin directory including trailing slash
     *
     * e.g. "http://www.your-domain.com/app/plugins/your-awesome-plugin/"
     *
     * @var string
     */
    protected $pluginUrl;

    /**
     * plugin name (name of the directory containing the plugin's index.php file)
     *
     * e.g. "your-awesome-plugin"
     *
     * @var string
     */
    protected $pluginName;

    /**
     * plugin slug as used by WordPress to identify plugins, e.g. for storing "active_plugins" in "wp_options" table
     * &rarr; it's just a concatenation of plugin name, slash and "index.php"
     *
     * e.g. "your-awesome-plugin/index.php"
     *
     * @var string
     */
    protected $pluginSlug;

    /**
     * PluginData constructor.
     *
     * @param string $pluginFile
     */
    public function __construct(string $pluginFile)
    {
        $this->setPluginFile($pluginFile);
    }

    /**
     * Sets the pluginFile as well as some other properties which rely on the given file path.
     *
     * @param string $pluginFile
     */
    protected function setPluginFile(string $pluginFile): void
    {
        $this->pluginFile = $pluginFile;

        //  also set other properites since they're dependant on the plugin file
        $this->pluginDir = plugin_dir_path($pluginFile);
        $this->pluginUrl = plugin_dir_url($pluginFile);
        $this->pluginName = basename($this->pluginDir);
        $this->pluginSlug = $this->pluginName . '/' . basename($pluginFile);
    }

    /**
     * Returns the pluginFile.
     *
     * @return string
     *
     * @see pluginFile
     */
    public function getPluginFile(): string
    {
        return $this->pluginFile;
    }

    /**
     * Returns the pluginDir.
     *
     * @return string
     *
     * @see pluginDir
     */
    public function getPluginDir(): string
    {
        return $this->pluginDir;
    }

    /**
     * Returns the pluginUrl.
     *
     * @return string
     *
     * @see pluginUrl
     */
    public function getPluginUrl(): string
    {
        return $this->pluginUrl;
    }

    /**
     * Returns the pluginName.
     *
     * @return string
     *
     * @see pluginName
     */
    public function getPluginName(): string
    {
        return $this->pluginName;
    }

    /**
     * Returns the pluginSlug.
     *
     * @return string
     *
     * @see pluginSlug
     */
    public function getPluginSlug(): string
    {
        return $this->pluginSlug;
    }
}
