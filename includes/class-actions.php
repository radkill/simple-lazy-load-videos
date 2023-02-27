<?php
if ( ! class_exists( 'SLLV_Actions' ) ) {
	/**
	 * Plugin actions
	 *
	 * Activation, deactivation, uninstalling
	 *
	 * @since 0.6.0
	 */
	class SLLV_Actions {

		/**
		 * Plugin activation actions
		 *
		 * @since 0.6.0
		 */
		public static function activate( $network_wide ) {
			$oembed_cache = new SLLV_Oembed_Cache();
			$oembed_cache->flush_all();
		}


		/**
		 * Plugin deactivation actions
		 *
		 * @since 0.6.0
		 */
		public static function deactivate() {
			$oembed_cache = new SLLV_Oembed_Cache();
			$oembed_cache->flush_all();
		}


		/**
		 * Plugin uninstalling actions
		 *
		 * @since 0.6.0
		 */
		public static function uninstall() {
			delete_option( 'sllv_version' );
			delete_option( 'sllv_settings' );
		}

	}
}
