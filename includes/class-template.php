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
		 * Youtube play button.
		 *
		 * @since 0.2.0
		 *
		 * @var string $youtube YouTube button code.
		 */
		private $youtube = '<svg width="68" height="48" viewBox="0 0 68 48">
				<path class="sllv-video__button-shape" d="M66.52,7.74c-0.78-2.93-2.49-5.41-5.42-6.19C55.79,.13,34,0,34,0S12.21,.13,6.9,1.55 C3.97,2.33,2.27,4.81,1.48,7.74C0.06,13.05,0,24,0,24s0.06,10.95,1.48,16.26c0.78,2.93,2.49,5.41,5.42,6.19 C12.21,47.87,34,48,34,48s21.79-0.13,27.1-1.55c2.93-0.78,4.64-3.26,5.42-6.19C67.94,34.95,68,24,68,24S67.94,13.05,66.52,7.74z"/>
				<path class="sllv-video__button-icon" d="M 45,24 27,14 27,34"/>
			</svg>';


		/**
		 * Vimeo play button.
		 *
		 * @since 0.2.0
		 *
		 * @var string $vimeo Vimeo button code.
		 */
		private $vimeo = '<svg width="76" height="43" viewBox="0 0 76 43">
				<path class="sllv-video__button-shape" d="M5,0H70.5a5.3,5.3,0,0,1,5.333,5V38A5.3,5.3,0,0,1,70.5,43H5a5,5,0,0,1-5-5V5A5,5,0,0,1,5,0Z"/>
				<path class="sllv-video__button-icon" d="M31.034,30.98L31,31V12l15.834,9.456Z"/>
			</svg>';


		/**
		 * Get YouTube button.
		 *
		 * @since 0.9.0
		 *
		 * @return string YouTube button SVG.
		 */
		public function get_youtube_button() {
			/**
			 * Filters the YouTube button code.
			 *
			 * @since 1.2.0
			 *
			 * @param string $this->youtube YouTube button SVG.
			 */
			return apply_filters( 'sllv_youtube_button', $this->youtube );
		}


		/**
		 * Get Vimeo button.
		 *
		 * @since 0.9.0
		 *
		 * @return string Vimeo button SVG.
		 */
		public function get_vimeo_button() {
			/**
			 * Filters the Vimeo button code.
			 *
			 * @since 1.2.0
			 *
			 * @param string $this->vimeo Vimeo button SVG.
			 */
			return apply_filters( 'sllv_vimeo_button', $this->vimeo );
		}


		/**
		 * Get oEmbed HTML from URL.
		 *
		 * @since 1.0.0
		 *
		 * @param  array $args Arguments.
		 * @return string       Returned video HTML.
		 */
		public function get_html_from_url( $args = array() ) {
			global $sllv;

			$args = wp_parse_args( $args, array(
				'url'       => '',
				'thumbnail' => false,
				'play'      => false,
				'hide_play' => false,
			) );

			$functions = new \SLLV\Functions();

			$output = false;

			if ( $args['play'] ) {
				$args['play'] = '<img class="" src="' . esc_url( $args['play'] ) . '">';
			}

			if ( $args['url'] ) {
				// Determine video from URL.
				$determine_video = $functions->determine_video_url( $args['url'] );

				// Build HTML if URL is video.
				if ( $determine_video['type'] ) {
					if ( 'youtube' === $determine_video['type'] ) {
						if ( ! $args['thumbnail'] ) {
							$args['thumbnail'] = $functions->get_youtube_thumb( $determine_video['id'], $sllv->get_settings( 'youtube_thumbnail_size' ) );
						}

						if ( ! $args['play'] && ! $args['hide_play'] ) {
							$args['play'] = $this->get_youtube_button();
						}
					} elseif ( 'vimeo' === $determine_video['type'] ) {
						if ( ! $args['thumbnail'] ) {
							$args['thumbnail'] = $functions->get_vimeo_thumb( $determine_video['id'], $sllv->get_settings( 'vimeo_thumbnail_size' ) );
						}

						if ( ! $args['play'] && ! $args['hide_play'] ) {
							$args['play'] = $this->get_vimeo_button();
						}
					}

					$output = $this->video( array(
						'provider'  => $determine_video['type'],
						'title'     => __( 'Video', 'simple-lazy-load-videos' ),
						'id'        => $determine_video['id'],
						'url'       => $args['url'],
						'thumbnail' => $args['thumbnail'],
						'play'      => $args['play'],
					) );
				}
			}

			return $output;
		}


		/**
		 * Video container.
		 *
		 * @since 0.2.0
		 *
		 * @param  array $args Arguments.
		 * @return string       Returned video HTML.
		 */
		public function video( $args = array() ) {
			$args = wp_parse_args( $args, array(
				'provider'  => '',
				'title'     => '',
				'id'        => '',
				'url'       => '',
				'thumbnail' => '',
				'play'      => '',
			) );

			ob_start();
			?>

			<div class="sllv-video -type_<?php echo esc_attr( $args['provider'] ); ?>" data-provider="<?php echo esc_attr( $args['provider'] ); ?>" data-video="<?php echo esc_attr( $args['id'] ); ?>">
				<a class="sllv-video__link" href="<?php echo esc_url( $args['url'] ); ?>" rel="external noopener" target="_blank">
					<img class="sllv-video__media" src="<?php echo esc_attr( $args['thumbnail'] ); ?>" alt="<?php echo esc_attr( $args['title'] ); ?>">
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
	}
}
