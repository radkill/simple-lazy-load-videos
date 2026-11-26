<?php
/**
 * Class Template.
 *
 * @package simple-lazy-load-videos
 * @since 0.2.0
 */

namespace SLLV;

if ( ! class_exists( '\SLLV\Template' ) ) {
	/**
	 * Template.
	 *
	 * @since 0.2.0
	 */
	class Template {

		/**
		 * Get YouTube button.
		 *
		 * @since 0.9.0
		 *
		 * @return string YouTube button SVG.
		 */
		private static function get_youtube_button() {
			$play_youtube = Functions::get_svg( 'play-youtube' );

			/**
			 * Filters the YouTube button code.
			 *
			 * @since 1.2.0
			 *
			 * @param string $play_button YouTube button SVG.
			 */
			return apply_filters( 'sllv_youtube_button', $play_youtube );
		}


		/**
		 * Get Vimeo button.
		 *
		 * @since 0.9.0
		 *
		 * @return string Vimeo button SVG.
		 */
		private static function get_vimeo_button() {
			$play_vimeo = Functions::get_svg( 'play-vimeo' );

			/**
			 * Filters the Vimeo button code.
			 *
			 * @since 1.2.0
			 *
			 * @param string $play_button Vimeo button SVG.
			 */
			return apply_filters( 'sllv_vimeo_button', $play_vimeo );
		}


		/**
		 * Get oEmbed HTML from URL.
		 *
		 * @since 1.0.0
		 *
		 * @param  array  $args Arguments.
		 * @return string       Returned video HTML.
		 */
		public static function get_html_from_url( $args = array() ) {
			$args = wp_parse_args(
				$args,
				array(
					'url'       => '',
					'thumbnail' => false,
					'play'      => false,
					'hide_play' => false,
				)
			);

			$output = false;

			if ( $args['hide_play'] ) {
				// `hide_play` wins over a custom `play` image.
				$args['play'] = false;
			} elseif ( $args['play'] ) {
				// Decorative: the button already has an aria-label.
				$args['play'] = '<img src="' . esc_url( $args['play'] ) . '" alt="">';
			}

			if ( $args['url'] ) {
				// Determine video from URL.
				$determine_video = Functions::determine_video_url( $args['url'] );

				// Build HTML if URL is video.
				if ( $determine_video['type'] ) {
					if ( 'youtube' === $determine_video['type'] ) {
						if ( ! $args['thumbnail'] ) {
							$args['thumbnail'] = Functions::get_youtube_thumb( $determine_video['id'], Plugin::get_settings( 'youtube_thumbnail_size' ) );
						}

						if ( ! $args['play'] && ! $args['hide_play'] ) {
							$args['play'] = self::get_youtube_button();
						}
					} elseif ( 'vimeo' === $determine_video['type'] ) {
						if ( ! $args['thumbnail'] ) {
							$args['thumbnail'] = Functions::get_vimeo_thumb( $determine_video['id'], Plugin::get_settings( 'vimeo_thumbnail_size' ), $determine_video['hash'] );
						}

						if ( ! $args['play'] && ! $args['hide_play'] ) {
							$args['play'] = self::get_vimeo_button();
						}
					}

					$output = self::video(
						array(
							'provider'  => $determine_video['type'],
							'title'     => __( 'Video', 'simple-lazy-load-videos' ),
							'id'        => $determine_video['id'],
							'hash'      => $determine_video['hash'],
							'start'     => $determine_video['start'],
							'url'       => $args['url'],
							'thumbnail' => $args['thumbnail'],
							'play'      => $args['play'],
						)
					);
				}
			}

			return $output;
		}


		/**
		 * Video container.
		 *
		 * @since 0.2.0
		 *
		 * @param  array  $args Arguments.
		 * @return string       Returned video HTML.
		 */
		private static function video( $args = array() ) {
			$args = wp_parse_args(
				$args,
				array(
					'provider'  => '',
					'title'     => '',
					'id'        => '',
					'hash'      => '',
					'start'     => 0,
					'url'       => '',
					'thumbnail' => '',
					'play'      => '',
				)
			);

			// Container attributes: the data attributes are read by the frontend JS.
			$video_attributes = array(
				'class'         => 'sllv-video -type_' . $args['provider'],
				'data-provider' => $args['provider'],
				'data-video'    => $args['id'],
			);

			if ( $args['hash'] ) {
				$video_attributes['data-hash'] = $args['hash'];
			}

			if ( $args['start'] ) {
				$video_attributes['data-start'] = (int) $args['start'];
			}

			$link_attributes = array(
				'class'  => 'sllv-video__link',
				'href'   => $args['url'],
				'rel'    => 'external noopener',
				'target' => '_blank',
			);

			// Without a thumbnail the link has no content, so give it an accessible name.
			if ( ! $args['thumbnail'] ) {
				$link_attributes['aria-label'] = $args['title'];
			}

			ob_start();
			?>

			<div<?php echo self::get_attributes( $video_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in get_attributes(). ?>>
				<a<?php echo self::get_attributes( $link_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in get_attributes(). ?>>
					<?php if ( $args['thumbnail'] ) : ?>
						<img class="sllv-video__media" src="<?php echo esc_url( $args['thumbnail'] ); ?>" alt="<?php echo esc_attr( $args['title'] ); ?>">
					<?php endif; ?>
				</a>
				<button class="sllv-video__button" type="button" aria-label="<?php esc_attr_e( 'Play Video', 'simple-lazy-load-videos' ); ?>"><?php echo $args['play']; ?></button>
			</div>

			<?php
			$output = ob_get_clean();

			/**
			 * Filters the video container HTML.
			 *
			 * @since 1.2.0
			 *
			 * @param string $output Returned video HTML.
			 * @param array  $args   Arguments.
			 */
			return apply_filters( 'sllv_video_template', $output, $args );
		}


		/**
		 * Build an escaped HTML attributes string.
		 *
		 * `href` and `src` values are escaped as URLs, all others as attributes.
		 *
		 * @since X.X.X
		 *
		 * @param  array $attributes Attribute name => value.
		 * @return string            Attributes with a leading space, like ` class="a" href="b"`.
		 */
		private static function get_attributes( $attributes ) {
			$output = '';

			foreach ( $attributes as $name => $value ) {
				if ( 'href' === $name || 'src' === $name ) {
					$value = esc_url( $value );
				} else {
					$value = esc_attr( $value );
				}

				$output .= ' ' . sanitize_key( $name ) . '="' . $value . '"';
			}

			return $output;
		}
	}
}
