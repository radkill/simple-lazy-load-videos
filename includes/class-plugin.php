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
		private static $settings_name = 'sllv_settings';


		/**
		 * Default settings.
		 *
		 * @since 0.7.2
		 *
		 * @var array $default Default settings.
		 */
		private static $default = array(
			'youtube_thumbnail_size' => 'sddefault',
			'vimeo_thumbnail_size'   => '640',
		);


		/**
		 * Class initialization.
		 *
		 * @since 0.6.0
		 */
		public function __construct() {
			new Resources();

			// Initialize admin components and database routines only in the dashboard.
			if ( is_admin() ) {
				new Options();

				// Admin initialization routine for DB updates and checks.
				add_action( 'admin_init', array( $this, 'admin_routine' ) );
			}

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
		public static function get_settings_name() {
			return self::$settings_name;
		}


		/**
		 * Get default settings.
		 *
		 * @since 1.10.0
		 *
		 * @param  string $option Optional. Setting name.
		 * @return array|string   All default settings, or one default value.
		 */
		public static function get_default_settings( $option = false ) {
			if ( $option ) {
				return isset( self::$default[ $option ] ) ? self::$default[ $option ] : '';
			}

			return self::$default;
		}


		/**
		 * Admin initialization routine for DB updates and checks.
		 *
		 * @since 1.8.0
		 */
		public function admin_routine() {
			$this->check_version();
			$this->check_options();
		}


		/**
		 * Plugin version check & update.
		 *
		 * @since 0.6.0
		 */
		private function check_version() {
			$version = get_option( 'sllv_version' );

			// If version changed.
			if ( ! $version || version_compare( $version, SLLV_VERSION, '!=' ) ) {
				update_option( 'sllv_version', SLLV_VERSION );
			}

			// Flush oEmbed cache if plugin update from version 0.9.0 or older.
			// Not on a fresh install: there is no old plugin cache to flush, only the site's own.
			if ( $version && version_compare( $version, '0.9.0', '<=' ) ) {
				Oembed_Cache::flush_old_cache();
			}
		}


		/**
		 * Create plugin options if not exist.
		 *
		 * @since 0.7.2
		 */
		private function check_options() {
			if ( ! get_option( self::get_settings_name() ) ) {
				add_option( self::get_settings_name(), self::$default );
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
		public static function get_settings( $option = false ) {
			$saved_options = get_option( self::get_settings_name(), array() );

			if ( ! is_array( $saved_options ) ) {
				$saved_options = array();
			}

			// Fill in any missing keys with defaults.
			$plugin_options = wp_parse_args( $saved_options, self::$default );

			if ( $option ) {
				return isset( $plugin_options[ $option ] ) ? $plugin_options[ $option ] : '';
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
			// Do replacement only on frontend, including content loaded via admin-ajax.php.
			if ( ! is_admin() || self::is_frontend_ajax() ) {
				// Just some video for test.
				// $url = 'https://youtu.be/D5LF3WChRrA';

				// Get oEmbed HTML from URL.
				$html = Template::get_html_from_url(
					array(
						'url' => $url,
					)
				);

				// Replace default HTML by custom if exist.
				if ( $html ) {
					// Remove line breaks and tabs to fix extra <br> in the final code.
					$cache = preg_replace( '/[\r\n\t]/', '', $html );
				}
			}

			return $cache;
		}


		/**
		 * Check if this is an AJAX request that may render frontend content.
		 *
		 * `is_admin()` is true in admin-ajax.php, so without this check videos in
		 * AJAX-loaded content ("load more", infinite scroll, filters) stay as iframes.
		 * The classic editor's embed preview (`parse-embed`) is excluded: there are
		 * no plugin scripts or styles in the editor.
		 *
		 * @since 1.10.0
		 *
		 * @return bool True for AJAX requests other than the editor embed preview.
		 */
		private static function is_frontend_ajax() {
			if ( ! wp_doing_ajax() ) {
				return false;
			}

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only reading the action name.
			$action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : '';

			return 'parse-embed' !== $action;
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
			// Get oEmbed HTML from URL.
			$html = Template::get_html_from_url(
				array(
					'url' => $url,
				)
			);

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
			$atts = shortcode_atts(
				array(
					'thumbnail' => false,
					'play'      => false,
					'hide_play' => false,
				),
				$atts
			);

			// The editor and wptexturize may add tags, whitespace and encoded `&` around/inside the URL.
			$url = str_replace( array( '&#038;', '&amp;' ), '&', wp_strip_all_tags( $content ) );

			// Get oEmbed HTML from URL.
			$html = Template::get_html_from_url(
				array(
					'url'       => $url,
					'thumbnail' => $atts['thumbnail'],
					'play'      => $atts['play'],
					'hide_play' => $atts['hide_play'],
				)
			);

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
		 *
		 * @param  array  $plugin_meta An array of the plugin's metadata.
		 * @param  string $plugin_file Path to the plugin file.
		 * @return array               An array of the plugin's metadata.
		 */
		public function add_plugin_row_meta( $plugin_meta, $plugin_file ) {

			if ( plugin_basename( SLLV_PATH . SLLV_PLUGIN_BASENAME ) === $plugin_file ) {
				$custom_meta = array(
					'changelog' => '<a href="https://github.com/radkill/simple-lazy-load-videos" target="_blank" style="font-weight: bold;">' . __( 'GitHub' ) . '</a>',
				);
				$plugin_meta = array_merge( $plugin_meta, $custom_meta );
			}

			return $plugin_meta;
		}
	}
}
