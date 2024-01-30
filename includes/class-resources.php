<?php
/**
 * Class Resources.
 *
 * @package simple-lazy-load-videos
 * @since 0.7.2
 */

namespace SLLV;

if ( ! class_exists( '\SLLV\Resources' ) ) {
	/**
	 * Resources.
	 *
	 * @since 0.7.2
	 */
	class Resources {

		/**
		 * Class initialization.
		 *
		 * @since 0.7.2
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		}


		/**
		 * Load plugin textdomain
		 *
		 * @since 0.2.0
		 */
		public function load_textdomain() {
			load_plugin_textdomain(
				'simple-lazy-load-videos',
				false,
				SLLV_PLUGIN_DIRNAME . '/languages/'
			);
		}


		/**
		 * Enqueue JavaScripts.
		 *
		 * @since 0.2.0
		 */
		public function enqueue_scripts() {
			if ( file_exists( SLLV_PATH . 'assets/js/scripts.js' ) ) {
				wp_enqueue_script(
					'sllv-main',
					SLLV_URL . 'assets/js/scripts.js',
					array( 'jquery' ),
					SLLV_VERSION,
					true
				);
			}
		}


		/**
		 * Enqueue CSS.
		 *
		 * @since 0.2.0
		 */
		public function enqueue_styles() {
			if ( file_exists( SLLV_PATH . 'assets/css/main.min.css' ) ) {
				wp_enqueue_style(
					'sllv-main',
					SLLV_URL . 'assets/css/main.min.css',
					false,
					SLLV_VERSION
				);
			}
		}
	}
}
