<?php
if ( ! class_exists( 'SLLV_Main' ) ) {
	class SLLV_Main {

		/**
		 * Settings name
		 */
		public $settings_name = 'sllv_settings';


		/**
		 * Default settings
		 */
		public $default = array(
			'youtube_thumbnail_size' => 'sddefault',
			'vimeo_thumbnail_size'   => '640',
		);


		/**
		 * Class initialization
		 */
		public function __construct() {
			new SLLV_Resources();
			new SLLV_Options();

			$this->check_version();
			$this->check_options();

			/** Register all hooks */
			add_filter( 'oembed_dataparse', array( $this, 'change_oembed' ), 10, 3 );
			add_action( 'save_post', array( $this, 'flush_oembed_cache' ), 10, 3 );
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
		 * Create plugin options if not exist
		 */
		public function check_options() {
			if ( ! get_option( $this->settings_name ) ) {
				add_option( $this->settings_name, $this->default );
			}

			if ( get_option( 'sllv' ) ) {
				delete_option( 'sllv' );
			}
		}


		/**
		 * Get options
		 */
		public function get_settings( $option = false ) {
			$plugin_options = get_option( $this->settings_name, $this->default );

			if ( $option ) {
				return $plugin_options[ $option ];
			}

			return $plugin_options;
		}


		/**
		 * Change video oEmbed
		 */
		function change_oembed( $return, $data, $url ) {
			$video = new SLLV_Template();

			if ( 'YouTube' === $data->provider_name ) {
				preg_match( "/embed\/([-\w]+)/", $data->html, $matches );
				$video_id = $matches[1];

				$return = $video->video( array(
					'provider'  => 'youtube',
					'title'     => $data->title,
					'id'        => $video_id,
					'url'       => 'https://youtu.be/' . $video_id,
					'thumbnail' => 'https://i.ytimg.com/vi/' . $video_id . '/' . $this->get_settings( 'youtube_thumbnail_size' ) . '.jpg',
					'play'      => $video->youtube,
				) );
			}

			if ( 'Vimeo' === $data->provider_name ) {
				$return = $video->video( array(
					'provider'  => 'vimeo',
					'title'     => $data->title,
					'id'        => $data->video_id,
					'url'       => 'https://vimeo.com/' . $data->video_id,
					'thumbnail' => substr( $data->thumbnail_url, 0, -3 ) . $this->get_settings( 'vimeo_thumbnail_size' ),
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
