# Development guide

This document guides you through the plugin development for WordPress with the [WP Plugin Core](https://github.com/Vierbeuter/wp-plugin-core) library.

## Requirements

* WordPress installation is up and running (we recommend using [Bedrock](https://roots.io/bedrock/))
* your WP installation is [Composer](https://getcomposer.org/)-ready (which it definately is when using Bedrock)
* `composer require wp-plugin-core` as described in the [README file](./../README.md) has been invoked to install the lib which is now available via vendor directory (see `./vendor/vierbeuter/wp-plugin-core/` in your project's docroot).

Alright, that's all we need. Let's get down to business!

**Are ya ready, kids?**

## Plugin boilerplate

Here you can download a ready-to-use plugin as a starting point for your development. Just download, unzip, move to plugins/ directory and change everything to your needs.

*// LINK TO PLUGIN BOILERPLATE FOLLOWS SOON …*

If you want to build your plugin from scratch go on reading to the next chapter.

## Creating a plugin from scratch

In the following let us create and implement your own plugin relying on the `wp-plugin-core` lib.

### 1) Create new folder for your plugin in the plugins directory of WordPress

Create a new folder `your-awesome-plugin/` in either "mu-plugins" or "plugins" directory. Let's start with following directory structure:

```bash
# within plugins/ dir
.
├── … # other plugins
├── your-awesome-plugin/   # add this folder
│   ├── languages/         # optional
│   │   └── .gitkeep       # optional
│   └── src/               # also add this one
└── …
```

*"Pretty easy up to now…"* – Yeah, and it won't get too hard. Promise. 😘

#### What is what?

##### `languages/`

Apparently, the `languages/` folder is where you can place the translations for your plugin.  
Unless you won't translate your plugin, just create an empty `.gitkeep` file inside it. We'll cover translations later on. In case you do not need translations at all just omit the `languages/` directory.

##### `src/`

The `src/` folder is where you will put your actual plugin code. We'll go into details in the next steps.

### 2) Create empty plugin files to prepare the real awesomeness

#### Plugin file(s)

The `wp-plugin-core` lib comes with an own autoloader. It uses namespaces, one folder for each namespace-part (reminds me of [PSR-0](http://www.php-fig.org/psr/psr-0/), right?).  
Furthermore, each plugin using the lib has to provide a (PHP) class representing the plugin itself.

That being said, let's assume you want to put all all your code into a namespace such as `\YourAwesomeCompany\AnyNamespace` and your class shall be named `YourAwesomePlugin` then the fully-qualified classname is as follows:

```
\YourAwesomeCompany\AnyNamespace\YourAwesomePlugin
```

The corresponding file path (due to the autoloader's implementation) needs to be:

```
YourAwesomeCompany/AnyNamespace/YourAwesomePlugin.php
```

This is what you have to put into the `src/` folder of `your-awesome-plugin`.

#### Bootstrap file

As usual for any WordPress plugin, you also need to have an `index.php` file containing some general meta data picked by WordPress to identify the plugin as well as some PHP code, in our case for bootstrapping the `Plugin` class.
  
Let's create empty files for that.

#### File overview

Your project structure should look like this:

```bash
.
├── …
├── your-awesome-plugin/
│   ├── index.php          # add this one
│   ├── languages/
│   │   └── .gitkeep
│   └── src/               # and your stuff in here
│       └── YourAwesomeCompany/
│           └── AnyNamespace/
│               └── YourAwesomePlugin.php
└── …
```

*"Still very easy."* – I told you. 😌

### 3) Breathe life into your plugin

#### `Plugin` class

Open `YourAwesomePlugin.php` file and insert the following code:

```php
<?php

namespace YourAwesomeCompany\AnyNamespace;

use Vierbeuter\WordPress\Plugin;

/**
 * The YourAwesomePlugin class represents the YourAwesomePlugin plugin.
 *
 * @package YourAwesomeCompany\AnyNamespace
 */
class YourAwesomePlugin extends Plugin
{

    /**
     * Initializes the plugin, e.g. adds features or services using the addFeature(…) and addComponent(…) methods.
     *
     * Example implementation:
     *
     * <code>
     * protected function initPlugin(): void
     * {
     *   //  add an awesome feature
     *   $this->addFeature(new AwesomeFeature());
     *   //  register an awesome service to DI-container
     *   $this->addComponent('service-handle', new AwesomeService());
     * }
     * </code>
     *
     * @see \Vierbeuter\WordPress\Plugin::addFeature()
     * @see \Vierbeuter\WordPress\Plugin::addComponent()
     */
    protected function initPlugin(): void
    {
        //  keep calm, we'll implement this method soon
    }
}
```

*"Holy crap! This one constists of more phpdoc than actual PHP!"* – Nice, right? 🤓

Your plugin class only has to extend the `Plugin` class provided by `wp-plugin-core`. It then has to implement the method `initPlugin()` which may be empty.

That's it.

#### `index.php` for bootstrapping

To be able to register our plugin class and all other classes we're gonna build upon that one, we now need to bootstrap the plugin.

Open your `index.php` and add the following few lines:

```php
<?php
/**
 * Plugin Name: Your Awesome Plugin
 * Description: This plugin provides awesome functionality used by awesome websites.
 * Author: Your Awesome Company
 * Author URI: http://www.your-awesome-website.com/
 */

\Vierbeuter\WordPress\Autoloader::register(__FILE__);
\YourAwesomeCompany\AnyNamespace\YourAwesomePlugin::activate(__FILE__);
```

*Wow, the whole bootstrapping is nothing more than a 2-liner…?* – Exactly. 😎

**At this point you have a functioning plugin.**  
It has no features yet but it doesn't cause errors though. It's something, isn't it?

### 4) Activate the plugin 

Open your WordPress admin panel ("/wp-admin" or "/wp/wp-admin" when using Bedrock) and activate your plugin.

We can now start implementing some features.

*"Yes, finally!"* – Yee-hah! 🤠

### 5) Add features to your plugin

#### Feature file(s)

Any functionality (such as hooking into WordPress actions or filters) is gonna be implemented in a `Feature` class. Add an empty one within your namespace. Optionally add a new folder to extend the namespace.

```bash
.
├── …
├── your-awesome-plugin/
│   ├── index.php
│   ├── languages/
│   │   └── .gitkeep
│   └── src/
│       └── YourAwesomeCompany/
│           └── AnyNamespace/
│               ├── Feature/   # add this one
│               │   └── TestFeature.php
│               └── YourAwesomePlugin.php
└── …
```

#### `Feature` class

Open the new file and paste the following code:

```php
<?php

namespace YourAwesomeCompany\AnyNamespace\Feature;

use Vierbeuter\WordPress\Feature\Feature;

/**
 * Our first feature implementation to play around with.
 *
 * @package YourAwesomeCompany\AnyNamespace\Feature
 */
class TestFeature extends Feature
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
            /** @see YourAwesomeCompany\AnyNamespace\Feature\TestFeature::wp_loaded() */
            'wp_loaded',
        ];
    }

    /**
     * Action hook implementation for "wp_loaded".
     *
     * Adds a test message.
     *
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference/wp_loaded
     */
    public function wp_loaded(): void
    {
        //  we need to pass a callback method, see method implementation below
        $this->addMessage([$this, 'printSuccess']);
    }

    /**
     * Callback method for printing success message.
     *
     * @see YourAwesomeCompany\AnyNamespace\Feature\TestFeature::wp_loaded()
     */
    public function printSuccess(): void
    {
        echo $this->getMessageMarkupSuccess('It works!', true);
    }
}
```

#### Register the feature

The `TestFeature` is done (see next section to get an explanation for what the feature is doing). Before anything happens we have to register it on our plugin.

Open the plugin class defined in `YourAwesomePlugin.php` and implement the `initPlugin()` method as follows:

```php
<?php

namespace YourAwesomeCompany\AnyNamespace;

use Vierbeuter\WordPress\Plugin;
//  do not forget to import the feature class
use YourAwesomeCompany\AnyNamespace\Feature\TestFeature;

class YourAwesomePlugin extends Plugin
{

    protected function initPlugin(): void
    {
        //  add your feature to the plugin
        $this->addFeature(new TestFeature());
    }
}
```

#### Explanation of what's happening here

It's some magic going on. But not that much actually, so don't worry.

##### `YourAwesomePlugin`

The last thing is pretty self-explaining: We just told our plugin to load the `TestFeature` class. It will be automatically activated. By "activating" I mean the feature is adding filter and action hooks to WP and so on.

##### `TestFeature`

The first method `getActionHooks()` in `TestFeature` class defines and returns a list of WP action hooks. Each action hook – in our case only `wp_loaded` – needs to match a same-named method in the `Feature` class which we defined below &rarr; see `wp_loaded()` method.
This method is called by WordPress whenever the `wp_loaded` action is processed.

What we got here is the easist kind of action hook: It has no parameters, it uses a default priority and the callback to be called by WP has the name as the hook itself.  
Of course, you can change the parameter count, the priority and the method name. But for simplicity let's just use it that way (by the way, parameters and priority are dependant on the type of hook).

See the `wp-plugin-core` library's source code to get to know what else you can return instead of single strings. Search for following trait and method (that trait is used by parent `Feature` class to import the methods):  
[`Vierbeuter\WordPress\Feature\Traits\HasWpHookSupport->getActionHooks()`](./../src/Vierbeuter/WordPress/Feature/Traits/HasWpHookSupport.php#L114)  
It has a default implementation (it's returning an empty array) and is pretty well documented. So, don't hesitate to have a look.

The `wp_loaded()` method calls `addMessage(…)` which delegates the task to another action hook of WordPress. Therefore we can't just pass a message string but have to pass a callable.  
No problem, we define a new method whose callable we pass to `addMessage(…)` and which then prints the success message using one of the helper methods that returns the proper HTML markup we need here.

See all methods of
[`Vierbeuter\WordPress\Feature\Traits\HasAdminNoticeSupport`](./../src/Vierbeuter/WordPress/Feature/Traits/HasAdminNoticeSupport.php) to get more insights.

##### Long story short

If you're familiar with WP plugin development and especially using hooks (filters and actions) you sure know what might happen with our plugin and the feature.

And you're absolutely right. The feature hooks into the `wp_loaded` action. Whenever WP fires this action our feature class is gonna be invoked (the method `wp_loaded()` will be called) which then adds a success message to the admin panel.

And that's our very first – yeah, maybe a bit senseless – WP plugin with an even more senseless feature.

### 6) What's next?

This is nothing more than a quick overview of what can be done and how does it have to be implemented the `wp-plugin-core` way.

To step deeper into the real plugin development please download the boilerplate and study its sample features.

*// LINK TO PLUGIN BOILERPLATE FOLLOWS SOON …*

#### 7) Translating your plugin

*// TO BE CONTINUED …*
