<?php
/**
 * Class Plugin
 *
 * @package simple-lazy-load-videos
 * @since 0.6.0
 */

namespace SLLV;

if ( ! class_exists( 'Main' ) ) {
	/**
	 * Main plugin class
	 *
	 * @since 0.6.0
	 */
	class Main {

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
			new \SLLV\Resources();
			new \SLLV\Options();

			// Plugin version check & update
			$this->check_version();

			// Create plugin options if not exist
			$this->check_options();

			// Change oEmbed HTML after cache by `embed_oembed_html`
			add_filter( 'embed_oembed_html', array( $this, 'change_oembed_html' ), 10, 4 );

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

			// If version changed
			if ( ! $version || version_compare( $version, SLLV_VERSION, '!=' ) ) {
				update_option( 'sllv_version', SLLV_VERSION );
			}

			// Flush oEmbed cache if plugin update from version 0.9.0 or older
			if ( ! $version || version_compare( $version, '0.9.0', '<=' ) ) {
				$oembed_cache = new \SLLV\Oembed_Cache();
				$oembed_cache->flush_old_cache();
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

			// Delete all plugin options (before v0.7.2)
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
		 * Change video oEmbed HTML
		 *
		 * @since 1.0.0
		 *
		 * @param  string|false $cache   The cached HTML result, stored in post meta.
		 * @param  string       $url     The attempted embed URL.
		 * @param  array        $attr    An array of shortcode attributes.
		 * @param  int          $post_ID Post ID.
		 * @return string                The returned oEmbed HTML
		 */
		public function change_oembed_html( $cache, $url, $attr, $post_ID ) {
			$template  = new \SLLV\Template();

			// do replacement only on frontend
			if ( ! is_admin() ) {
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
			}

			return $cache;
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

			$template = new \SLLV\Template();

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
