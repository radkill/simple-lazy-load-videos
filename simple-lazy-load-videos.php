<?php
/**
 * Plugin Name:       Simple Lazy Load Videos
 * Plugin URI:        https://github.com/radkill/simple-lazy-load-videos
 * Description:       Simple Lazy Load for embedded video from Youtube and Vimeo
 * Version:           0.6.3
 * Requires PHP:      5.6
 * Author:            Valerii Bohdanov
 * Author URI:        https://profiles.wordpress.org/rad_/
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       simple-lazy-load-videos
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); // Exit if accessed directly

if ( ! defined( 'SLLV_URL' ) ) {
	define( 'SLLV_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'SLLV_PATH' ) ) {
	define( 'SLLV_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'SLLV_PLUGIN_DIRNAME' ) ) {
	define( 'SLLV_PLUGIN_DIRNAME', dirname( plugin_basename( __FILE__ ) ) );
}

if ( ! defined( 'SLLV_VERSION' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_data = get_plugin_data( __FILE__ );
	define( 'SLLV_VERSION', $plugin_data['Version'] );
}


/**
 * Classes
 */
require_once( SLLV_PATH . 'includes/class-actions.php' );
require_once( SLLV_PATH . 'includes/class-main.php' );
require_once( SLLV_PATH . 'includes/class-oembed-cache.php' );
require_once( SLLV_PATH . 'includes/class-template.php' );


/**
 * Activation, deactivation and uninstalling actions
 */
register_activation_hook( __FILE__, array( 'SLLV_Actions', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'SLLV_Actions', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( 'SLLV_Actions', 'uninstall' ) );


/**
 * Start plugin
 */
add_action( 'plugins_loaded', function() {
	new SLLV_main();
} );
