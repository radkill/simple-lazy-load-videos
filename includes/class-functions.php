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

			if ( '' === $cached_result ) {
				// Cached failure.
				return false;
			} elseif ( false !== $cached_result ) {
				// Get cached result if exists.
				$body = $cached_result;
			} else {
				// Use WordPress HTTP API to get response.
				$request = wp_remote_get( $api_url, $args );

				// Check if request was successful and returned a valid 200 OK status.
				if ( is_wp_error( $request ) || 200 !== wp_remote_retrieve_response_code( $request ) ) {
					// Cache the failure for a shorter time, so the API isn't requested on every page view.
					if ( $expiration ) {
						set_transient( $cache_key, '', min( $expiration, HOUR_IN_SECONDS ) );
					}

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
		 * @return array       Video type & ID, plus Vimeo privacy hash and YouTube start time (seconds) if present.
		 */
		public static function determine_video_url( $url ) {
			$video_hash  = '';
			$video_start = 0;

			// Video ID may follow `v=` anywhere in the query string, or one of the known paths.
			// Other URLs (e.g. playlists) don't match and keep the default WordPress embed.
			$youtube_pattern = '~^(?:(?:https?:)?//)?(?:(?:www|m)\.)?(?:youtube\.com/(?:watch\?(?:\S*?&)?v=|embed/|v/|shorts/|live/)|youtu\.be/)([\w\-]+)~i';
			$vimeo_pattern   = '/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/';

			if ( false !== strpos( $url, 'youtu' ) && preg_match( $youtube_pattern, $url, $youtube_matches ) ) {
				$video_type = 'youtube';
				$video_id   = $youtube_matches[1];

				// Start time: `t=90`, `t=90s`, `t=1m30s` or `start=90`.
				if ( preg_match( '~[?&#](?:t|start)=([0-9hms]+)~i', $url, $time_matches ) ) {
					$video_start = self::parse_time( $time_matches[1] );
				}
			} elseif ( false !== strpos( $url, 'vimeo' ) && preg_match( $vimeo_pattern, $url, $vimeo_matches ) ) {
				$video_type = 'vimeo';
				$video_id   = $vimeo_matches[5];

				// Unlisted videos have a privacy hash: `vimeo.com/ID/HASH` or `player.vimeo.com/video/ID?h=HASH`.
				if ( preg_match( '~vimeo\.com/(?:[a-z]*/)*[0-9]{6,11}/([0-9a-f]+)~i', $url, $hash_matches ) || preg_match( '~[?&]h=([0-9a-f]+)~i', $url, $hash_matches ) ) {
					$video_hash = $hash_matches[1];
				}
			} else {
				$video_type = false;
				$video_id   = 0;
			}

			$data = array(
				'type'  => $video_type,
				'id'    => $video_id,
				'hash'  => $video_hash,
				'start' => $video_start,
			);

			return $data;
		}


		/**
		 * Convert a time value like `90`, `90s` or `1h2m3s` to seconds.
		 *
		 * @since X.X.X
		 *
		 * @param  string $time Time value.
		 * @return int          Seconds.
		 */
		private static function parse_time( $time ) {
			if ( preg_match( '/^\d+$/', $time ) ) {
				return (int) $time;
			}

			$seconds    = 0;
			$multiplier = array(
				'h' => 3600,
				'm' => 60,
				's' => 1,
			);

			if ( preg_match_all( '~(\d+)([hms])~i', $time, $parts, PREG_SET_ORDER ) ) {
				foreach ( $parts as $part ) {
					$seconds += (int) $part[1] * $multiplier[ strtolower( $part[2] ) ];
				}
			}

			return $seconds;
		}


		/**
		 * Get YouTube thumbnail.
		 *
		 * Return WEBP if exists, otherwise JPG. If the requested size doesn't exist
		 * (sddefault / maxresdefault are missing for some videos), fall back to hqdefault,
		 * which always exists. Otherwise YouTube shows a gray placeholder.
		 *
		 * @since 0.8.0
		 *
		 * @param  string $video_id YouTube video ID.
		 * @param  string $size     Thumbnail size: hqdefault / sddefault / maxresdefault.
		 * @return string           Thumbnail URL.
		 */
		public static function get_youtube_thumb( $video_id, $size = 'sddefault' ) {
			// Build URLs for both formats.
			$url_webp     = 'https://i.ytimg.com/vi_webp/' . $video_id . '/' . $size . '.webp';
			$url_jpg      = 'https://i.ytimg.com/vi/' . $video_id . '/' . $size . '.jpg';
			$url_fallback = 'https://i.ytimg.com/vi/' . $video_id . '/hqdefault.jpg';

			if ( self::check_thumbnail_exists( $url_webp ) ) {
				$thumbnail_url = $url_webp;
			} elseif ( 'hqdefault' === $size || self::check_thumbnail_exists( $url_jpg ) ) {
				$thumbnail_url = $url_jpg;
			} else {
				$thumbnail_url = $url_fallback;
			}

			return $thumbnail_url;
		}


		/**
		 * Check if thumbnail exists.
		 *
		 * @since 1.7.0
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
		 * @param  string $hash     Optional. Privacy hash of an unlisted video.
		 * @return string           Thumbnail URL.
		 */
		public static function get_vimeo_thumb( $video_id, $size = '640', $hash = '' ) {
			// The simple API doesn't return unlisted videos, so use oEmbed with the hash for them.
			if ( $hash ) {
				$data = self::remote_api_get(
					add_query_arg(
						array(
							'url'   => rawurlencode( 'https://vimeo.com/' . $video_id . '/' . $hash ),
							'width' => (int) $size,
						),
						'https://vimeo.com/api/oembed.json'
					)
				);

				return isset( $data->thumbnail_url ) ? $data->thumbnail_url : '';
			}

			$data = self::remote_api_get( 'https://vimeo.com/api/v2/video/' . $video_id . '.json' );

			if ( empty( $data ) || ! isset( $data[0]->thumbnail_large ) ) {
				$thumbnail_url = '';
			} else {
				$thumbnail_url = str_replace( '-d_640', '-d_' . $size, $data[0]->thumbnail_large );
			}

			return $thumbnail_url;
		}


		/**
		 * Get SVG code.
		 *
		 * @since 1.7.0
		 *
		 * @param  string $file File name.
		 * @return string       SVG code.
		 */
		public static function get_svg( $file ) {
			static $svg_cache = array();

			if ( isset( $svg_cache[ $file ] ) ) {
				return $svg_cache[ $file ];
			}

			$image_path = SLLV_PATH . 'assets/img/' . $file . '.svg';

			if ( file_exists( $image_path ) ) {
				$play_button = file_get_contents( $image_path );
			} else {
				$play_button = '';
			}

			$svg_cache[ $file ] = $play_button;

			return $play_button;
		}
	}
}
