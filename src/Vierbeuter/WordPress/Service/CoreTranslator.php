<?php

namespace Vierbeuter\WordPress\Service;

/**
 * The CoreTranslator service provides methods for translating texts of WP Plugin Core.
 *
 * @package Vierbeuter\WordPress\Service
 */
class CoreTranslator extends Translator
{

    /**
     * CoreTranslator constructor.
     */
    public function __construct()
    {
        parent::__construct('vb-wp-plugin-core', realpath(__DIR__ . '/../../../../languages/'));
    }
}
