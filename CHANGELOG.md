### 1.7.1
* Fix PHP error

### 1.7.0
* Get YouTube thumbnails in WEBP format if exists
* Fix "Links are not crawlable" issue
* Remove line breaks and tabs to fix extra `<br>` in the final code.
* Move SVG to separate files
* Refactor Functions::remote_api_get() method
* Replace creating new instance of classes with static methods
* Fix methods visibility
* Remove global $sllv variable
* Fix WPCS compliance
* Update Grunt packages
* Tested up to WordPress 6.8.1

### 1.6.0
*	Add new search key in preg_match for short YouTube videos (thanks @paulooliveirar)
* Fix WPCS compliance
* Update Grunt packages

### 1.5.1
* Fix Notice "Function \_load_textdomain_just_in_time was called incorrectly"
* Fix visibility of some methods
* Remove grunt-spritesmith
* Update Grunt packages
* Tested up to WordPress 6.7.1

### 1.5.0
* Add shortcode attributes: thumbnail, play, hide_play
* Update FAQ
* Update Grunt packages
* Tested up to WordPress 6.6.2

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
* Fix WPCS compliance
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

### 0.9.0
* Stop all other video or HTML media if new video starts playing
* Stop all video if HTML media starts playing
* Code refactoring
* Fix documentation standards

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

### 0.6.8
* Fixed styles for WPBakery Page Builder

### 0.6.7
* Fixed styles for WPBakery Page Builder

### 0.6.6
* Fixed some broken styles

### 0.6.5
* Fixed styles for Divi Builder

### 0.6.4
* Fixed some PHP warnings that occurred during actions with the plugin

### 0.6.3
* Removed Ukrainian translation since it is now available at translate.wordpress.org

### 0.6.2
* Removed native Lazy Loading

### 0.6.1
* Removed Russian translation since it is now available at translate.wordpress.org
* Updated Ukrainian translation

### 0.6
* Changed CSS & JS fileversion, now it is plugin version
* Сode refactoring

### 0.5.3
* Some small fixes

### 0.5.2
* Fixed some bug with image width & height

### 0.5.1
* Fixed compatible with Visual Composer
* Updated ukrainian translate
* Improved compatibility

### 0.5
* Fixed fatal error on some installs
* Fixed file_exists check for scripts & styles

### 0.4
* Native Lazy Loading for images
* Some small fix

### 0.3.4
* Improved compatibility
* Fixed grammar error

### 0.3.3
* Translated to Ukrainian and Russian

### 0.3.2
* Changed css prefix for greater compatibility

### 0.3.1
* Fixed compatible with language packs (again)

### 0.3
* Fixed compatible with language packs
* Some small fix

### 0.2
* Update play button for Vimeo version
* Fix in WP Dashboard
* Added multilang support

### 0.1
* Initial release.
