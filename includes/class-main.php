<?php
if ( ! class_exists( 'SLLV_main' ) ) {
	class SLLV_main {

		/**
		 * Class initialization
		 */
		public function __construct() {
			$this->check_version();

			/** Register all hooks */
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'after_setup_theme', array( $this, 'editor_style' ) );

			add_filter( 'oembed_dataparse', array( $this, 'change_oembed' ), 10, 3 );
			add_action( 'save_post', array( $this, 'flush_oembed_cache' ), 10, 3 );
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


		/**
		 * Plugin version check & update
		 */
		public function check_version() {
			$version = get_option( 'sllv_version' );

			if ( ! $version || version_compare( $version, SLLV_VERSION, '!=' ) ) {
				$oembed_cache = new SLLV_Oembed_Cache();
				$oembed_cache->flush_all();

				update_option( 'sllv_version', SLLV_VERSION );
			}
		}


		/**
		 * Change video oEmbed
		 */
		function change_oembed( $return, $data, $url ) {
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


		/**
		 * Flush oembed cache
		 */
		public function flush_oembed_cache( $post_ID, $post, $update ) {
			$oembed_cache = new SLLV_Oembed_Cache();
			$oembed_cache->flush( $post_ID );
		}

	}
}