<?php
/**
 * Plugin Name:       Simple Lazy Load Videos
 * Plugin URI:        https://github.com/radkill/simple-lazy-load-videos
 * Description:       Simple Lazy Load for embedded video from Youtube and Vimeo.
 * Version:           1.6.0
 * Requires at least: 4.9
 * Requires PHP:      5.6
 * Author:            Valerii Bohdanov
 * Author URI:        https://profiles.wordpress.org/rad_/
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       simple-lazy-load-videos
 * Domain Path:       /languages
 *
 * @package simple-lazy-load-videos
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
}


/**
 * Constants.
 *
 * SLLV_PATH            = plugin dir path, like '/var/www/html/wp-content/plugins/simple-lazy-load-videos/'
 * SLLV_URL             = plugin url, like 'https://sitename.com/wp-content/plugins/simple-lazy-load-videos/'
 * SLLV_PLUGIN_DIRNAME  = plugin dirname, like 'simple-lazy-load-videos'
 * SLLV_PLUGIN_BASENAME = plugin main file basename, like 'simple-lazy-load-videos.php'
 * SLLV_VERSION         = plugin version
 */
if ( ! defined( 'SLLV_PATH' ) ) {
	define( 'SLLV_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'SLLV_URL' ) ) {
	define( 'SLLV_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'SLLV_PLUGIN_DIRNAME' ) ) {
	define( 'SLLV_PLUGIN_DIRNAME', dirname( plugin_basename( __FILE__ ) ) );
}

if ( ! defined( 'SLLV_PLUGIN_BASENAME' ) ) {
	define( 'SLLV_PLUGIN_BASENAME', basename( __FILE__ ) );
}

if ( ! defined( 'SLLV_VERSION' ) ) {
	define( 'SLLV_VERSION', '1.6.0' );
}


/**
 * Classes.
 */
require_once SLLV_PATH . 'includes/class-actions.php';
require_once SLLV_PATH . 'includes/class-plugin.php';
require_once SLLV_PATH . 'includes/class-resources.php';
require_once SLLV_PATH . 'includes/class-oembed-cache.php';
require_once SLLV_PATH . 'includes/class-template.php';
require_once SLLV_PATH . 'includes/class-options.php';
require_once SLLV_PATH . 'includes/class-functions.php';


/**
 * Activation, deactivation and uninstalling actions.
 */
register_activation_hook( __FILE__, array( 'SLLV\Actions', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'SLLV\Actions', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( 'SLLV\Actions', 'uninstall' ) );


/**
 * Start plugin.
 */
add_action( 'plugins_loaded', function() {
	new \SLLV\Plugin();
} );
