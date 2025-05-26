<?php
/**
 * Class Functions.
 *
 * @package simple-lazy-load-videos
 * @since 0.8.0
 */

namespace SLLV;

if ( ! class_exists( '\SLLV\Functions' ) ) {
	/**
	 * Functions.
	 *
	 * @since 0.8.0
	 */
	class Functions {

		/**
		 * Get remote JSON & cache with Transients API.
		 *
		 * @since 0.8.0
		 *
		 * @param  string $api_url    URL to retrieve.
		 * @param  array  $args       Optional. Request arguments. Default empty array. See WP_Http::request() for information on accepted arguments.
		 * @param  int    $expiration Time until expiration in seconds.
		 * @return object|false       The response or false on failure.
		 */
		public static function remote_api_get( $api_url, $args = array(), $expiration = DAY_IN_SECONDS ) {
			// Create cache key based on URL.
			$cache_key     = 'sllv_cache_' . md5( $api_url );
			$cached_result = get_transient( $cache_key );

			if ( false !== $cached_result ) {
				// Get cached result if exists.
				$body = $cached_result;
			} else {
				// Use WordPress HTTP API to get response.
				$request = wp_remote_get( $api_url, $args );

				// Check if request was successful.
				if ( is_wp_error( $request ) ) {
					return false;
				}

				$body = wp_remote_retrieve_body( $request );

				// Cache result.
				if ( $expiration ) {
					set_transient( $cache_key, $body, $expiration );
				}
			}

			return json_decode( $body );
		}


		/**
		 * Get YouTube/Vimeo video type & ID.
		 *
		 * @since 0.8.0
		 *
		 * @param  string $url YouTube or Vimeo video URL.
		 * @return array       Video type & ID.
		 */
		public static function determine_video_url( $url ) {
			$is_match_youtube = preg_match( '/^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/|shorts\/)?)([\w\-]+)(\S+)?$/', $url, $youtube_matches );

			$is_match_vimeo = preg_match( '/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/', $url, $vimeo_matches );

			if ( $is_match_youtube ) {
				$video_type = 'youtube';
				$video_id   = $youtube_matches[5];
			} elseif ( $is_match_vimeo ) {
				$video_type = 'vimeo';
				$video_id   = $vimeo_matches[5];
			} else {
				$video_type = false;
				$video_id   = 0;
			}

			$data = array(
				'type' => $video_type,
				'id'   => $video_id,
			);

			return $data;
		}


		/**
		 * Get YouTube thumbnail.
		 *
		 * Return WEBP if exists, otherwise fallback to JPG.
		 *
		 * @since 0.8.0
		 *
		 * @param  string $video_id YouTube video ID.
		 * @param  string $size     Thumbnail size: hqdefault / sddefault / maxresdefault.
		 * @return string           Thumbnail URL.
		 */
		public static function get_youtube_thumb( $video_id, $size = 'sddefault' ) {
			// Build URLs for both formats.
			$url_webp = 'https://i.ytimg.com/vi_webp/' . $video_id . '/' . $size . '.webp';
			$url_jpg  = 'https://i.ytimg.com/vi/' . $video_id . '/' . $size . '.jpg';

			// Use WEBP if exists, otherwise fallback to JPG.
			if ( self::check_thumbnail_exists( $url_webp ) ) {
				$thumbnail_url = $url_webp;
			} else {
				$thumbnail_url = $url_jpg;
			}

			return $thumbnail_url;
		}


		/**
		 * Check if thumbnail exists.
		 *
		 * @since X.X.X
		 *
		 * @param  string $url        Thumbnail URL to check.
		 * @param  int    $expiration Time until expiration in seconds.
		 * @return bool               True if exists, false otherwise.
		 */
		private static function check_thumbnail_exists( $url, $expiration = DAY_IN_SECONDS ) {
			// Create cache key based on URL.
			$cache_key     = 'sllv_cache_' . md5( $url );
			$cached_result = get_transient( $cache_key );

			if ( false !== $cached_result ) {
				// Get cached result if exists.
				$exists = (bool) $cached_result;
			} else {
				// Use WordPress HTTP API to check URL.
				$response = wp_remote_head(
					$url,
					array(
						'timeout'     => 5,
						'redirection' => 0,
					)
				);

				// Check if request was successful.
				if ( is_wp_error( $response ) ) {
					$exists = false;
				} else {
					// Check HTTP status code.
					$status_code = wp_remote_retrieve_response_code( $response );

					if ( 200 === $status_code ) {
						$exists = true;
					} else {
						$exists = false;
					}
				}

				// Cache result.
				if ( $expiration ) {
					set_transient( $cache_key, $exists ? 1 : 0, $expiration );
				}
			}

			return $exists;
		}


		/**
		 * Get Vimeo thumbnail.
		 *
		 * @since 0.8.0
		 *
		 * @param  string $video_id Vimeo video ID.
		 * @param  string $size     Thumbnail size: 640 / 1280.
		 * @return string           Thumbnail URL.
		 */
		public static function get_vimeo_thumb( $video_id, $size = '640' ) {
			$data          = self::remote_api_get( 'https://vimeo.com/api/v2/video/' . $video_id . '.json' );
			$thumbnail_url = str_replace( '-d_640', '-d_' . $size, $data[0]->thumbnail_large );

			return $thumbnail_url;
		}


		/**
		 * Get SVG code.
		 *
		 * @since X.X.X
		 *
		 * @param  string $file File name.
		 * @return string       SVG code.
		 */
		public static function get_svg( $file ) {
			$image_path = SLLV_PATH . 'assets/img/' . $file . '.svg';

			if ( file_exists( $image_path ) ) {
				$play_button = file_get_contents( $image_path );
			} else {
				$play_button = '';
			}

			return $play_button;
		}
	}
}
