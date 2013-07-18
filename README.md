Church Theme Framework
======================

A library of code from [churchthemes.com](http://churchthemes.com) useful for assisting with the development of church WordPress themes using the [Church Theme Content](https://github.com/churchthemes/church-theme-content) functionality plugin.

Purpose
-------

Church Theme Framework is a drop-in framework. In other words, it's a directory of includes containing functions, classes and other code useful to multiple, similar themes.

* Faster development
* Easier maintenance
* Consistent features

Features
--------  

Developers can use as many or as few framework features as needed.

* Integration with the [Church Theme Content](https://github.com/churchthemes/church-theme-content) plugin for sermons, events, people and locations
* Require a minimum version of WordPress and Internet Explorer
* Load translation file from *wp-content/languages/themes/textdomain-locale.mo* for safe updates
* Useful ``add_theme_support`` features, helper functions and constants
* Helper function for detecting current content "section" (sermons, blog, events, etc.)
* Helper functions for displaying native galleries (posts using gallery, counts for a gallery, gallery cover, etc.)
* Sermon archives by year, month and day
* Podcasting and recurring events provided by the Church Theme Content plugin
* Correct prev/next sorting of events (event date), locations and people (manual order)
* Flexible widgets for posts, sermons, events, locations, people, galleries, taxonomies, giving, slides and highlights
* Widget output is controlled by template files
* Ability to disable or override specific widget fields
* Restrict certain widgets to specific sidebars and vice-versa
* Support for multiple color schemes
* Support for preset backgrounds in Theme Customizer
* Support for Google Fonts and responsive Google Maps
* Responsive video and audio embeds plus generic branding
* Featured image sizing notes, support for upscaling
* Custom image sizes for galleries based on number of columns
* Ability to force "Save As" of file in media library via special URL (for "Download" buttons)
* Automatic friendly ``<title>``'s with page numbering (can be overridden by SEO plugins)
* Automatic breadcrumb path considering archives, taxonomies, post types, attachments, etc.
* Support for attachments to inherit parent post's discussion status
* Automatic URL replacement and menu location setting for imported sample content

Developers
----------

The framework was developed for our use at [churchthemes.com](http://churchthemes.com). We decided to make it public for others who build church sites with WordPress.

Please note that while we do not intend to introduce breaking changes, we reserve the right to change course in future versions as necessary for our own development needs. We do not provide development support but if you find a bug, please submit an issue and we will take a look.

### Before you start

You will need to be familiar with the [Church Theme Content](http://wordpress.org/plugins/church-theme-content) plugin ([GitHub repository](https://github.com/churchthemes/church-theme-content)) since the framework is made specifically for building themes that use the post types, taxonomies and custom fields that the plugin provides.

This guide assumes you are proficient in [WordPress Theme Development](http://codex.wordpress.org/Theme_Development).

### Add the framework to your theme

#### Manually

1. Create a *framework* directory in your theme containing the contents of this repository ([zip](https://github.com/churchthemes/church-theme-framework/archive/master.zip))
2. Add the contents of the [CT Meta Box](https://github.com/churchthemes/ct-meta-box) repository ([zip](https://github.com/churchthemes/ct-meta-box/archive/master.zip)) to *framework/includes/libraries/ct-meta-box*

#### Automatically with Git

If you use Git, you can clone the repository and get the CT Meta Box submodule in one shot.

```bash
cd path-to-your-theme
git clone --recursive https://github.com/churchthemes/church-theme-framework.git framework
```

### Include the main framework script

Add this to your theme's **functions.php** file.

```php
/**
 * Load framework
 */
require_once get_template_directory() . '/framework/framework.php'; // do this before anything
```

### An overview of usage

You will need to be very familiar with the framework's code in order to leverage it. Please browse the contents of this repository in order to understand how the framework is built. This guide is very basic and only covers getting started. If you find this framework does more than you need, you may consider developing your theme with support for the [Church Theme Content](https://github.com/churchthemes/church-theme-content) plugin apart from this framework. The post types, taxonomies and custom fields will be directly available to you whether or not this framework is used.

#### Adding support for features

There are many framework features you can enable using WordPress's ``add_theme_support`` function. Here are [code search results](https://github.com/churchthemes/church-theme-framework/search?q=current_theme_supports+OR+get_theme_support&type=Code) showing where these features are implemented. Some work without additional aguments while many require specific configuration.

#### Helper functions

There are several helper functions included to assist you with development. Browse the scripts in the [includes](https://github.com/churchthemes/church-theme-framework/tree/master/includes) directory to become acquainted.

#### Constants

[framework.php](https://github.com/churchthemes/church-theme-framework/blob/master/framework.php) defines several constants pertaining to the theme and framework that you may find useful.

### A complete example

Browse the code from one of our commercial themes at [churchthemes.com](http://churchthemes.com) for a complete example of a finished theme using this framework.