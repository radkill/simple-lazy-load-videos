<?php
if ( ! class_exists( 'SLLV_Actions' ) ) {
	class SLLV_Actions {

		/**
		 * Plugin activation actions
		 */
		public static function activate( $network_wide ) {
			$oembed_cache = new SLLV_Oembed_Cache();
			$oembed_cache->flush_all();
		}


		/**
		 * Plugin deactivation actions
		 */
		public static function deactivate() {
			$oembed_cache = new SLLV_Oembed_Cache();
			$oembed_cache->flush_all();
		}


		/**
		 * Plugin uninstalling actions
		 */
		public static function uninstall() {
			delete_option( 'sllv_version' );
      delete_option( 'sllv_settings' );
		}

	}
}
