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
### 1.4.1
* Fix missing jQuery error

### 1.4.0
* Add BuddyPress support
* Tested up to WordPress 6.4.2

### 1.3.0
* Remove styles from admin_enqueue_scripts
* Remove old admin styles from CSS
* Replace dart-sass to sass
* Change esversion in jshint config to 8
* Update packages
* Fix svg
* Fix WPCS
* Tested up to WordPress 6.4.1

### 1.2.0
* Add filters: `sllv_youtube_button`, `sllv_vimeo_button`, `sllv_video_template`
* Remove jQuery from depends scripts
* Rename CSS & JS handles
* Tested up to WordPress 6.3

### 1.1.0
* Refactoring
* Add GitHub link to plugin meta

### 1.0.0
* Add Gutenberg support
* New oEmbed template logic
* Do template replacement only on frontend
* Fix Vimeo thumbnail

[View full changelog](https://github.com/radkill/simple-lazy-load-videos/blob/master/CHANGELOG.md)
