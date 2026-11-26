<?php
/**
 * Class Actions.
 *
 * @package simple-lazy-load-videos
 * @since 0.6.0
 */

namespace SLLV;

if ( ! class_exists( '\SLLV\Actions' ) ) {
	/**
	 * Plugin actions.
	 *
	 * Activation, deactivation and uninstalling actions
	 *
	 * @since 0.6.0
	 */
	class Actions {

		/**
		 * Plugin activation actions.
		 *
		 * @since 0.6.0
		 *
		 * @param bool $network_wide Is the plugin enabled for all sites on the network.
		 */
		public static function activate( $network_wide ) {
		}


		/**
		 * Plugin deactivation actions.
		 *
		 * @since 0.6.0
		 */
		public static function deactivate() {
		}


		/**
		 * Plugin uninstalling actions.
		 *
		 * @since 0.6.0
		 */
		public static function uninstall() {
			delete_option( 'sllv_version' );
			delete_option( 'sllv_settings' );

			self::delete_cache();
		}


		/**
		 * Delete cached remote requests (thumbnails, Vimeo API).
		 *
		 * Expired transients stay in the database until WordPress cleans them up,
		 * so remove them all on uninstall. With a persistent object cache they are
		 * not in the database and simply expire.
		 *
		 * @since X.X.X
		 */
		private static function delete_cache() {
			global $wpdb;

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Removing plugin data on uninstall.
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM $wpdb->options
						WHERE `option_name` LIKE %s
							OR `option_name` LIKE %s",
					$wpdb->esc_like( '_transient_sllv_cache_' ) . '%',
					$wpdb->esc_like( '_transient_timeout_sllv_cache_' ) . '%'
				)
			);
		}
	}
}
