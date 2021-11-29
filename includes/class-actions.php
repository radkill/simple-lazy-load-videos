<?php
if ( ! class_exists( 'SLLV_Actions' ) ) {
	class SLLV_Actions {

		/**
		 * Activate actions
		 */
		public function activate( $network_wide ) {
			$oembed_cache = new SLLV_Oembed_Cache();
			$oembed_cache->flush_all();
		}


		/**
		 * Deactivate actions
		 */
		public function deactivate() {
			$oembed_cache = new SLLV_Oembed_Cache();
			$oembed_cache->flush_all();
		}


		/**
		 * Uninstall actions
		 */
		public function uninstall() {
			delete_option( 'sllv_version' );
		}

	}
}
