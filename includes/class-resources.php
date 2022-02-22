<?php
if ( ! class_exists( 'SLLV_Resources' ) ) {
	class SLLV_Resources {

		/**
		 * Class initialization
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'after_setup_theme', array( $this, 'editor_style' ) );
		}


		/**
		 * Load plugin textdomain
		 */
		function load_textdomain() {
			load_plugin_textdomain( 'simple-lazy-load-videos', false, SLLV_PLUGIN_DIRNAME . '/languages/' );
		}


		/**
		 * Enqueue JavaScripts
		 */
		public function enqueue_scripts() {
			if ( file_exists( SLLV_PATH . 'assets/js/scripts.js' ) ) {
				wp_enqueue_script( 'sllv-js-main', SLLV_URL . 'assets/js/scripts.js', array( 'jquery' ), SLLV_VERSION, true );
			}
		}


		/**
		 * Enqueue CSS
		 */
		public function enqueue_styles() {
			if ( file_exists( SLLV_PATH . 'assets/css/main.min.css' ) ) {
				wp_enqueue_style( 'sllv-css-main', SLLV_URL . 'assets/css/main.min.css', false, SLLV_VERSION );
			}
		}


		/**
		 * Editor CSS
		 */
		public function editor_style() {
			if ( file_exists( SLLV_PATH . 'assets/css/main.min.css' ) ) {
				add_editor_style( SLLV_URL . 'assets/css/main.min.css' );
			}
		}
	}
}
