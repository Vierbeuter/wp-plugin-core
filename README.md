# WP Plugin Core

*// WORK IN PROGRESS, I RECOMMEND YOU TO WAIT UNTIL A FINAL RELEASE IS PUBLISHED*

The library is pretty close to release 1.0 though. See the project's [roadmap](./ROADMAP.md) for what can be expected in the near and far future.

## What's this?

The **WP Plugin Core** library provides base classes for developing WordPress plugins. It enables you to easily build your stuff in an object-oriented way.

## What's in it?

A lot, I would say… Let's start with a few buzz words:

* object orientation
* dependency injection
* easy WP hooks
* custom post-types
* custom fields
* custom taxonomies
* translations / i18n
* custom pages for wp-admin panel
* config pages for `wp_options` (also translatable)
* REST API endpoints
* WPML support

… to only name some of the major features and characteristics of **WP Plugin Core**.

See the [change logs](./CHANGELOG.md) for details of what you can expect by using the latest release, for example.

## Can I use it?

This library is supposed to be used with [Composer](https://getcomposer.org/). You can integrate it in your standard WordPress installation though (by copying the sources to your own WP plugin and manually configuring an autoloader etc.). But for ease of use it's recommended to use Composer.

Since [Bedrock](https://roots.io/bedrock/) supports `composer` it's a perfect match. Using Bedrock isn't a bad choice at all.

## May I use it?

Of course, you may. This library is licensed under the terms of the **MIT license**. See also the project's [license file](./LICENSE).

## Let's use it!

### Add dependency using `composer`

Alright, to setup this lib for your project just invoke the following command on terminal:

```bash
# assuming you're in the project's root directory
# (this is where your composer.json is located)
composer require vierbeuter/wp-plugin-core
```

### Implement your custom plugin

To get some help on implementing your own WordPress plugin using the `wp-plugin-core` library have a look into the [implementation guide](./doc/HOW-TO.md).
