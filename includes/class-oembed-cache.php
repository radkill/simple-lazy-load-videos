<?php
if ( ! class_exists( 'SLLV_Oembed_Cache' ) ) {
	class SLLV_Oembed_Cache {

		/**
		 * Flush all oembed caches
		 */
		public function flush_all() {
			global $wpdb;

			$meta_key_1 = "|_oembed|_%%";
			$meta_key_2 = "|_oembed|_time|_%%";

			$wpdb->query(
				$query = $wpdb->prepare(
					"DELETE FROM `" . $wpdb->postmeta . "`
						WHERE `meta_key` LIKE %s ESCAPE '|'
							OR `meta_key` LIKE %s ESCAPE '|'",
					$meta_key_1,
					$meta_key_2
				)
			);
		}

		/**
		 * Flush oembed cache for single post
		 */
		public function flush( $post_id ) {
			global $wpdb;

			$meta_key_1 = "|_oembed|_%%";
			$meta_key_2 = "|_oembed|_time|_%%";

			$wpdb->query(
				$query = $wpdb->prepare(
					"DELETE FROM `" . $wpdb->postmeta . "`
						WHERE post_id = %d
							AND (
								`meta_key` LIKE %s ESCAPE '|'
									OR `meta_key` LIKE %s ESCAPE '|'
							)",
					$post_id,
					$meta_key_1,
					$meta_key_2
				)
			);
		}

	}
}
