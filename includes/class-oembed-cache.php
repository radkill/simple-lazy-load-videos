<?php
/**
 * Class Oembed_Cache.
 *
 * @package simple-lazy-load-videos
 * @since 0.2.0
 */

namespace SLLV;

if ( ! class_exists( '\SLLV\Oembed_Cache' ) ) {
	/**
	 * Oembed Cache.
	 *
	 * @since 0.2.0
	 */
	class Oembed_Cache {

		/**
		 * Flush all old oembed caches.
		 *
		 * Used if plugin version is 0.9.0 or older
		 *
		 * @since 1.0.0
		 */
		public function flush_old_cache() {
			global $wpdb;

			$meta_key_1  = '|_oembed|_%%';
			$meta_key_2  = '|_oembed|_time|_%%';
			$option_name = '|_transient_oembed|_%%';

			// Flush common cache.
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM $wpdb->postmeta
						WHERE `meta_key` LIKE %s ESCAPE '|'
							OR `meta_key` LIKE %s ESCAPE '|'",
					$meta_key_1,
					$meta_key_2
				)
			);

			// Flush Gutenberg cache.
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM $wpdb->options
						WHERE `option_name` LIKE %s ESCAPE '|'",
					$option_name
				)
			);
		}
	}
}
