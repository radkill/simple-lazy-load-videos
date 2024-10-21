<?php
/**
 * Class Plugin.
 *
 * @package simple-lazy-load-videos
 * @since 0.6.0
 */

namespace SLLV;

if ( ! class_exists( '\SLLV\Plugin' ) ) {
	/**
	 * Main plugin class.
	 *
	 * @since 0.6.0
	 */
	class Plugin {

		/**
		 * Options name for settings.
		 *
		 * @since 0.7.2
		 *
		 * @var string $settings_name Settings name.
		 */
		private $settings_name = 'sllv_settings';


		/**
		 * Default settings.
		 *
		 * @since 0.7.2
		 *
		 * @var array $default Default settings.
		 */
		private $default = array(
			'youtube_thumbnail_size' => 'sddefault',
			'vimeo_thumbnail_size'   => '640',
		);


		/**
		 * Class initialization.
		 *
		 * @since 0.6.0
		 */
		public function __construct() {
			new \SLLV\Resources();
			new \SLLV\Options();

			// Plugin version check & update.
			$this->check_version();

			// Create plugin options if not exist.
			$this->check_options();

			// Change oEmbed HTML after cache by `embed_oembed_html`.
			add_filter( 'embed_oembed_html', array( $this, 'change_oembed_html' ), 10, 4 );

			// Change video oEmbed HTML for BuddyPress.
			add_filter( 'bp_embed_oembed_html', array( $this, 'bp_change_oembed_html' ), 10, 4 );

			// Add shortcodes.
			add_shortcode( 'sllv_video', array( $this, 'shortcode' ) );

			// Add plugin row meta.
			add_filter( 'plugin_row_meta', array( $this, 'add_plugin_row_meta' ), 10, 2 );
		}


		/**
		 * Get option name for settings.
		 *
		 * @since 0.9.0
		 *
		 * @return string Option name for settings.
		 */
		public function get_settings_name() {
			return $this->settings_name;
		}


		/**
		 * Plugin version check & update.
		 *
		 * @since 0.6.0
		 */
		public function check_version() {
			$version = get_option( 'sllv_version' );

			// If version changed.
			if ( ! $version || version_compare( $version, SLLV_VERSION, '!=' ) ) {
				update_option( 'sllv_version', SLLV_VERSION );
			}

			// Flush oEmbed cache if plugin update from version 0.9.0 or older.
			if ( ! $version || version_compare( $version, '0.9.0', '<=' ) ) {
				$oembed_cache = new \SLLV\Oembed_Cache();
				$oembed_cache->flush_old_cache();
			}
		}


		/**
		 * Create plugin options if not exist.
		 *
		 * @since 0.7.2
		 */
		public function check_options() {
			if ( ! get_option( $this->get_settings_name() ) ) {
				add_option( $this->get_settings_name(), $this->default );
			}

			// Delete all plugin options (before v0.7.2).
			if ( get_option( 'sllv' ) ) {
				delete_option( 'sllv' );
			}
		}


		/**
		 * Get options.
		 *
		 * @since 0.7.2
		 *
		 * @param  string $option Option name.
		 * @return array|string   Plugin settings.
		 */
		public function get_settings( $option = false ) {
			$plugin_options = get_option( $this->get_settings_name(), $this->default );

			if ( $option ) {
				return $plugin_options[ $option ];
			}

			return $plugin_options;
		}


		/**
		 * Change video oEmbed HTML.
		 *
		 * @since 1.0.0
		 *
		 * @param  string|false $cache   The cached HTML result, stored in post meta.
		 * @param  string       $url     The attempted embed URL.
		 * @param  array        $attr    An array of shortcode attributes.
		 * @param  int          $post_ID Post ID.
		 * @return string                The returned oEmbed HTML.
		 */
		public function change_oembed_html( $cache, $url, $attr, $post_ID ) {
			$template = new \SLLV\Template();

			// do replacement only on frontend.
			if ( ! is_admin() ) {
				// Just some video for test.
				// $url = 'https://youtu.be/D5LF3WChRrA';

				// Get oEmbed HTML from URL.
				$html = $template->get_html_from_url( array(
					'url' => $url,
				) );

				// Replace default HTML by custom if exist.
				if ( $html ) {
					$cache = $html;
				}
			}

			return $cache;
		}


		/**
		 * Change video oEmbed HTML for BuddyPress.
		 *
		 * @since 1.4.0
		 *
		 * @param  string $cache   Cached HTML markup for embed.
		 * @param  string $url     The URL being embedded.
		 * @param  array  $attr    Parsed shortcode attributes.
		 * @param  array  $rawattr Unparased shortcode attributes.
		 * @return string          The returned oEmbed HTML.
		 */
		public function bp_change_oembed_html( $cache, $url, $attr, $rawattr ) {
			$template = new \SLLV\Template();

			// Get oEmbed HTML from URL.
			$html = $template->get_html_from_url( array(
				'url' => $url,
			) );

			// Replace default HTML by custom if exist.
			if ( $html ) {
				$cache = $html;
			}

			return $cache;
		}


		/**
		 * Shortcode [sllv_video].
		 *
		 * @since 0.8.0
		 *
		 * @param  array  $atts    Shortcode attributes.
		 * @param  string $content Shortcode content.
		 * @return string          Returned shortcode HTML.
		 */
		public function shortcode( $atts, $content = '' ) {
			$atts = shortcode_atts( array(
				'thumbnail' => false,
				'play'      => false,
				'hide_play' => false,
			), $atts );

			$template = new \SLLV\Template();

			// Get oEmbed HTML from URL.
			$html = $template->get_html_from_url( array(
				'url'       => $content,
				'thumbnail' => $atts['thumbnail'],
				'play'      => $atts['play'],
				'hide_play' => $atts['hide_play'],
			) );

			// if oEmbed HTML not exist then show shortcode content.
			if ( $html ) {
				$output = $html;
			} else {
				$output = $content;
			}

			return $output;
		}

		/**
		 * Filters the array of row meta for each/specific plugin in the Plugins list table.
		 * Appends additional links below each/specific plugin on the plugins page.
		 *
		 * @since 1.1.0
		 * @link https://team.baeldung.com/browse/UX-6840
		 *
		 * @param  array  $plugin_meta An array of the plugin's metadata.
		 * @param  string $plugin_file Path to the plugin file.
		 * @return array               An array of the plugin's metadata.
		 */
		public function add_plugin_row_meta( $plugin_meta, $plugin_file ) {

			if ( strpos( $plugin_file, SLLV_PLUGIN_BASENAME ) ) {
				$custom_meta = array(
					'changelog' => '<a href="https://github.com/radkill/simple-lazy-load-videos" target="_blank" style="font-weight: bold;">' . __( 'GitHub' ) . '</a>',
				);
				$plugin_meta = array_merge( $plugin_meta, $custom_meta );
			}

			return $plugin_meta;
		}
	}
}
