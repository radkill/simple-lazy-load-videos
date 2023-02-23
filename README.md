# Simple Lazy Load Videos

> Simple Lazy Load for embedded video from Youtube and Vimeo

[![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/simple-lazy-load-videos)](https://wordpress.org/plugins/simple-lazy-load-videos/)
[![WordPress Plugin Required PHP Version](https://img.shields.io/wordpress/plugin/required-php/simple-lazy-load-videos)](https://wordpress.org/plugins/simple-lazy-load-videos/)
[![WordPress Plugin: Required WP Version](https://img.shields.io/wordpress/plugin/wp-version/simple-lazy-load-videos)](https://wordpress.org/plugins/simple-lazy-load-videos/)
[![WordPress Plugin: Tested WP Version](https://img.shields.io/wordpress/plugin/tested/simple-lazy-load-videos)](https://wordpress.org/plugins/simple-lazy-load-videos/)
[![License](https://img.shields.io/github/license/radkill/simple-lazy-load-videos)](https://github.com/radkill/simple-lazy-load-videos/blob/master/LICENSE)

[![WordPress Plugin Last Updated](https://img.shields.io/wordpress/plugin/last-updated/simple-lazy-load-videos)](https://wordpress.org/plugins/simple-lazy-load-videos/)
[![WordPress Plugin Active Installs](https://img.shields.io/wordpress/plugin/installs/simple-lazy-load-videos)](https://wordpress.org/plugins/simple-lazy-load-videos/)
[![WordPress Plugin Rating](https://img.shields.io/wordpress/plugin/rating/simple-lazy-load-videos)](https://wordpress.org/plugins/simple-lazy-load-videos/#reviews)

[![WP compatibility](https://plugintests.com/plugins/wporg/simple-lazy-load-videos/wp-badge.svg)](https://plugintests.com/plugins/wporg/simple-lazy-load-videos/latest)
[![PHP compatibility](https://plugintests.com/plugins/wporg/simple-lazy-load-videos/php-badge.svg)](https://plugintests.com/plugins/wporg/simple-lazy-load-videos/latest)

## Description
The plugin reduces page load time and increases your Google PageSpeed score.

It replaces the embedded Youtube and Vimeo videos with a video preview image, third-party CSS & JS are downloaded only after a click.

## Installation
1. Upload the plugin to your WordPress site
2. Activate the plugin in the Plugin dashboard

## Frequently Asked Questions
### How does this plugin work?
Instead of loading the iframe of your video on page load, it only loads the video preview image.
This will work automatically for all videos that have been inserted through the standard page editor.

### Is it possible to insert a video using a shortcode?
Yes, you can use the `[sllv_video]` shortcode using a link to a YouTube or Vimeo video as its value, like:
```php
[sllv_video]https://youtu.be/GywDFkY3z-c[/sllv_video]
```

### Can I embed a lazy loaded video into my template file?
Yes, but you cannot insert a shortcode `[sllv_video]` into your template file directly.
You will need to pass the code into apply_shortcodes() function and display its output like this:
```php
<?php echo apply_shortcodes( '[sllv_video]https://youtu.be/GywDFkY3z-c[/sllv_video]' ); ?>
```

## Changelog
### 0.8.2
* Add a check if the link in the shortcode is a video link

### 0.8.1
* Fix the missing file problem

### 0.8
* Add shortcode to display SLLV in theme templates
* Update Frequently Asked Questions
* Small code refactor

### 0.7.6
* Fix editor styles
* Add margins between videos

### 0.7.5
* Add some data clearing

### 0.7.4
* Fix aspect ratio in some themes
* Fix "PHP Warning: Undefined property: stdClass::$title" if title is empty for any reason

### 0.7.3
* Repair params in embed youtube
* Delete plugin settings if uninstall
* Сode refactoring
* Tested up to WordPress 6.1

### 0.7.2
* Сode refactoring

### 0.7.1
* Made it possible to change Vimeo thumbnail size

### 0.7.0
* Сode refactoring
* Added plugins options page
* Made it possible to change YouTube thumbnail size

[View full changelog](https://github.com/radkill/simple-lazy-load-videos/blob/master/CHANGELOG.md)
