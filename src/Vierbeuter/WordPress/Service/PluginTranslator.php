<?php

namespace Vierbeuter\WordPress\Service;

use Vierbeuter\WordPress\PluginData;

/**
 * The PluginTranslator service provides methods for translating texts of the plugin.
 *
 * @package Vierbeuter\WordPress\Service
 */
class PluginTranslator extends Translator
{

    /**
     * CoreTranslator constructor.
     *
     * @param \Vierbeuter\WordPress\PluginData $pluginData
     */
    public function __construct(PluginData $pluginData)
    {
        parent::__construct($pluginData->getPluginName(), $pluginData->getPluginDir() . 'languages/');
    }
}
