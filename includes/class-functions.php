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
		 * @param  string $api_url    URL of remote json file.
		 * @param  int    $expiration Time until expiration in seconds.
		 * @return object             Remote API as object.
		 */
		public function remote_api_get( $api_url, $expiration = DAY_IN_SECONDS ) {
			$api_url_hash = 'sllv_cache_' . md5( $api_url );
			$cache        = get_transient( $api_url_hash );

			if ( $cache ) {
				$body = $cache;
			} else {
				$request = wp_remote_get( $api_url );

				if ( is_wp_error( $request ) ) {
					return false;
				}

				$body = wp_remote_retrieve_body( $request );

				if ( $expiration ) {
					set_transient( $api_url_hash, $body, $expiration );
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
		public function determine_video_url( $url ) {
			$is_match_youtube = preg_match( '/^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$/', $url, $youtube_matches );

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
		 * @since 0.8.0
		 *
		 * @param  string $video_id YouTube video ID.
		 * @param  string $size     Thumbnail size: hqdefault / sddefault / maxresdefault.
		 * @return string           Thumbnail URL.
		 */
		public function get_youtube_thumb( $video_id, $size = 'sddefault' ) {
			$thumbnail_url = 'https://i.ytimg.com/vi/' . $video_id . '/' . $size . '.jpg';

			return $thumbnail_url;
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
		public function get_vimeo_thumb( $video_id, $size = '640' ) {
			$data          = $this->remote_api_get( 'https://vimeo.com/api/v2/video/' . $video_id . '.json' );
			$thumbnail_url = str_replace( '-d_640', '-d_' . $size, $data[0]->thumbnail_large );

			return $thumbnail_url;
		}
	}
}
