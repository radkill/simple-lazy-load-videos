<?php
/**
 * Plugin Name:       Simple Lazy Load Videos
 * Plugin URI:        https://github.com/radkill/simple-lazy-load-videos
 * Description:       Simple Lazy Load Videos from Youtube & Vimeo
 * Version:           0.3
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

if ( ! defined( 'SLLV_VERSION' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_data = get_plugin_data( __FILE__ );
	define( 'SLLV_VERSION', $plugin_data['Version'] );
}

require_once( SLLV_PATH . 'includes/plugin-styles.php' );
require_once( SLLV_PATH . 'includes/plugin-scripts.php' );

require_once( SLLV_PATH . 'includes/class-oembed-cache.php' );
require_once( SLLV_PATH . 'includes/class-template.php' );

register_activation_hook( __FILE__, 'sllv_plugin_activate' );
register_deactivation_hook( __FILE__, 'sllv_plugin_deactivate' );
register_uninstall_hook( __FILE__, 'sllv_plugin_uninstall');


/**
 * Activate
 */
function sllv_plugin_activate() {
	$oembed_cache = new SLLV_Oembed_Cache();
	$oembed_cache->flush_all();
}


/**
 * Deactivate
 */
function sllv_plugin_deactivate() {
	$oembed_cache = new SLLV_Oembed_Cache();
	$oembed_cache->flush_all();
}


/**
 * Uninstall
 */
function sllv_plugin_uninstall() {
	delete_option( 'sllv_version' );
}


/**
 * Plugin version check & update
 */
if ( ! function_exists( 'sllv_check_version' ) ) {
	function sllv_check_version() {
		$version = get_option( 'sllv_version' );

		if ( ! $version || version_compare( $version, SLLV_VERSION, '!=' ) ) {
			$oembed_cache = new SLLV_Oembed_Cache();
			$oembed_cache->flush_all();

			update_option( 'sllv_version', SLLV_VERSION );
		}
	}
}
sllv_check_version();


/**
 * Flush oembed cache on post update
 */
if ( ! function_exists( 'sllv_flush_oembed_cache' ) ) {
	add_action( 'save_post', 'sllv_flush_oembed_cache', 10, 3 );
	function sllv_flush_oembed_cache( $post_ID, $post, $update ) {
		$oembed_cache = new SLLV_Oembed_Cache();
		$oembed_cache->flush( $post_ID );
	}
}


/**
 * Lazy Load Videos
 */
if ( ! function_exists( 'sllv_change_oembed' ) ) {
	add_filter( 'oembed_dataparse', 'sllv_change_oembed', 10, 3 );
	function sllv_change_oembed( $return, $data, $url ) {
		$video = new SLLV_template();

		if ( 'YouTube' === $data->provider_name ) {
			preg_match( "/embed\/([-\w]+)\?feature=/", $data->html, $matches );
			$video_id = $matches[1];

			$return = $video->video( array(
				'provider'  => 'youtube',
				'title'     => $data->title,
				'id'        => $video_id,
				'url'       => 'https://youtu.be/' . $video_id,
				'thumbnail' => 'https://i.ytimg.com/vi/' . $video_id . '/hqdefault.jpg',
				'play'      => $video->youtube,
			) );
		}

		if ( 'Vimeo' === $data->provider_name ) {
			$return = $video->video( array(
				'provider'  => 'vimeo',
				'title'     => $data->title,
				'id'        => $data->video_id,
				'url'       => 'https://vimeo.com/' . $data->video_id,
				'thumbnail' => $data->thumbnail_url,
				'play'      => $video->vimeo,
			) );
		}

		return $return;
	}
}
