<?php

namespace Vierbeuter\WordPress\Feature;

/**
 * Adds new sizes for image-thumbs using the add_image_size() function of WP.
 *
 * @package Vierbeuter\WordPress\Feature
 *
 * @see https://developer.wordpress.org/reference/functions/add_image_size/
 */
abstract class AddImageSizes extends Feature
{

    /**
     * key to set width value
     */
    const THUMB_CONFIG_WIDTH = 'width';

    /**
     * key to set height value
     */
    const THUMB_CONFIG_HEIGHT = 'height';

    /**
     * key to set crop value
     */
    const THUMB_CONFIG_CROP = 'crop';

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
            'wp_loaded',
        ];
    }

    /**
     * Hooks into "wp_loaded" to add new thumb sizes as defined in getImageSizes() method.
     *
     * @see \Vierbeuter\WordPress\Feature\AddImageSizes::getImageSizes()
     */
    public static function wp_loaded(): void
    {
        foreach (static::getImageSizes() as $name => $config) {
            $width = $config[static::THUMB_CONFIG_WIDTH];
            $height = $config[static::THUMB_CONFIG_HEIGHT];
            $crop = isset($config[static::THUMB_CONFIG_CROP]) ? $config[static::THUMB_CONFIG_CROP] : true;

            add_image_size($name, $width, $height, $crop);
        }
    }

    /**
     * Returns a list of image size configs to be added to WP using add_image_size(…) function.
     *
     * Example implementation:
     * <code>
     * protected static function getImageSizes(): array
     * {
     * return [
     *     'thumb_name' => [
     *       static::THUMB_CONFIG_WIDTH => 321,
     *       static::THUMB_CONFIG_HEIGHT => 123,
     *       static::THUMB_CONFIG_CROP => true,    //  optional, default is TRUE
     *     ],
     *     //  …
     *   ];
     * }
     * </code>
     *
     * @return array
     *
     * @see add_image_size()
     */
    abstract protected static function getImageSizes(): array;
}
