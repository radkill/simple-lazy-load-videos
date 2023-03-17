<?php
if ( ! class_exists( 'SLLV_Main' ) ) {
	/**
	 * Main
	 *
	 * @since 0.6.0
	 */
	class SLLV_Main {

		/**
		 * Options name for settings
		 *
		 * @since 0.7.2
		 */
		private $settings_name = 'sllv_settings';


		/**
		 * Default settings
		 *
		 * @since 0.7.2
		 */
		private $default = array(
			'youtube_thumbnail_size' => 'sddefault',
			'vimeo_thumbnail_size'   => '640',
		);


		/**
		 * Class initialization
		 *
		 * @since 0.6.0
		 */
		public function __construct() {
			new SLLV_Resources();
			new SLLV_Options();

			// Plugin version check & update
			$this->check_version();

			// Create plugin options if not exist
			$this->check_options();

			// Change oEmbed HTML before cache by `oembed_dataparse`
			add_filter( 'oembed_dataparse', array( $this, 'change_oembed' ), 10, 3 );

			// Change oEmbed HTML after cache by `embed_oembed_html`
			// add_filter( 'embed_oembed_html', array( $this, 'change_oembed_html' ), 10, 4 );

			// Flush oembed cache if save post
			add_action( 'save_post', array( $this, 'flush_oembed_cache' ), 10, 3 );

			// Add shortcodes
			add_shortcode( 'sllv_video', array( $this, 'shortcode' ) );
		}


		/**
		 * Get option name for settings
		 *
		 * @since 0.9.0
		 *
		 * @return string Option name for settings
		 */
		public function get_settings_name() {
			return $this->settings_name;
		}


		/**
		 * Plugin version check & update
		 *
		 * @since 0.6.0
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
		 *
		 * @since 0.7.2
		 */
		public function check_options() {
			if ( ! get_option( $this->get_settings_name() ) ) {
				add_option( $this->get_settings_name(), $this->default );
			}

			if ( get_option( 'sllv' ) ) {
				delete_option( 'sllv' );
			}
		}


		/**
		 * Get options
		 *
		 * @since 0.7.2
		 *
		 * @param  string       $option Option name
		 * @return array|string         Plugin settings
		 */
		public function get_settings( $option = false ) {
			$plugin_options = get_option( $this->get_settings_name(), $this->default );

			if ( $option ) {
				return $plugin_options[ $option ];
			}

			return $plugin_options;
		}


		/**
		 * Change video oEmbed
		 *
		 * @since 0.6.0
		 *
		 * @param  string $return The returned oEmbed HTML.
		 * @param  object $data   A data object result from an oEmbed provider.
		 * @param  string $url    The URL of the content to be embedded.
		 * @return string         The returned oEmbed HTML
		 */
		public function change_oembed( $return, $data, $url ) {
			$template = new SLLV_Template();

			if ( isset( $data->title ) ) {
				$video_title = $data->title;
			} else {
				$video_title = __( 'Video', 'simple-lazy-load-videos' );
			}

			if ( 'YouTube' === $data->provider_name ) {
				preg_match( "/embed\/([-\w]+)/", $data->html, $matches );
				$video_id = $matches[1];

				$return = $template->video( array(
					'provider'  => 'youtube',
					'title'     => $video_title,
					'id'        => $video_id,
					'url'       => 'https://youtu.be/' . $video_id,
					'thumbnail' => 'https://i.ytimg.com/vi/' . $video_id . '/' . $this->get_settings( 'youtube_thumbnail_size' ) . '.jpg',
					'play'      => $template->get_youtube_button(),
				) );
			} elseif ( 'Vimeo' === $data->provider_name ) {
				$return = $template->video( array(
					'provider'  => 'vimeo',
					'title'     => $video_title,
					'id'        => $data->video_id,
					'url'       => 'https://vimeo.com/' . $data->video_id,
					'thumbnail' => substr( $data->thumbnail_url, 0, -3 ) . $this->get_settings( 'vimeo_thumbnail_size' ),
					'play'      => $template->get_vimeo_button(),
				) );
			}

			return $return;
		}


		/**
		 * Change video oEmbed HTML
		 *
		 * @since X.X.X
		 *
		 * @param  string|false $cache   The cached HTML result, stored in post meta.
		 * @param  string       $url     The attempted embed URL.
		 * @param  array        $attr    An array of shortcode attributes.
		 * @param  int          $post_ID Post ID.
		 * @return string                The returned oEmbed HTML
		 */
		public function change_oembed_html( $cache, $url, $attr, $post_ID ) {
			$template  = new SLLV_Template();

			// Just some video for test
			// $url = 'https://youtu.be/D5LF3WChRrA';

			// Get oEmbed HTML from URL
			$html = $template->get_html_from_url( array(
				'url' => $url,
			) );

			// replace default HTML by custom if exist
			if ( $html ) {
				$cache = $html;
			}

			return $cache;
		}


		/**
		 * Flush oembed cache
		 *
		 * @since 0.6.0
		 *
		 * @param int     $post_ID Post ID.
		 * @param WP_Post $post    Post object.
		 * @param bool    $update  Whether this is an existing post being updated.
		 */
		public function flush_oembed_cache( $post_ID, $post, $update ) {
			$oembed_cache = new SLLV_Oembed_Cache();
			$oembed_cache->flush( $post_ID );
		}


		/**
		 * Shortcode [sllv_video]
		 *
		 * @since 0.8.0
		 *
		 * @param  array  $atts    Shortcode attributes
		 * @param  string $content Shortcode content
		 * @return string          Returned shortcode HTML
		 */
		public function shortcode( $atts, $content = '' ) {
			$atts   = shortcode_atts( array(

			), $atts );

			$template = new SLLV_Template();

			// Get oEmbed HTML from URL
			$html = $template->get_html_from_url( array(
				'url' => $content,
			) );

			// if oEmbed HTML not exist then show shortcode content
			if ( $html ) {
				$output = $html;
			} else {
				$output = $content;
			}

			return $output;
		}

	}
}
