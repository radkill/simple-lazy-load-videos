<?php
if ( ! class_exists( 'SLLV_Template' ) ) {
	class SLLV_Template {

		/**
		 * Youtube play button
		 */
		public $youtube = '
			<svg width="68" height="48" viewBox="0 0 68 48">
				<path class="sllv-video__button-shape" d="M66.52,7.74c-0.78-2.93-2.49-5.41-5.42-6.19C55.79,.13,34,0,34,0S12.21,.13,6.9,1.55 C3.97,2.33,2.27,4.81,1.48,7.74C0.06,13.05,0,24,0,24s0.06,10.95,1.48,16.26c0.78,2.93,2.49,5.41,5.42,6.19 C12.21,47.87,34,48,34,48s21.79-0.13,27.1-1.55c2.93-0.78,4.64-3.26,5.42-6.19C67.94,34.95,68,24,68,24S67.94,13.05,66.52,7.74z"></path>
				<path class="sllv-video__button-icon" d="M 45,24 27,14 27,34"></path>
			</svg>';

		/**
		 * Vimeo play button
		 */
		public $vimeo = '
			<svg width="76" height="43" viewBox="0 0 76 43">
				<path class="sllv-video__button-shape" d="M5,0H70.5a5.3,5.3,0,0,1,5.333,5V38A5.3,5.3,0,0,1,70.5,43H5a5,5,0,0,1-5-5V5A5,5,0,0,1,5,0Z"/>
				<path class="sllv-video__button-icon" d="M31.034,30.98L31,31V12l15.834,9.456Z"/>
			</svg>';

		/**
		 * Video container
		 */
		public function video( $args = array() ) {
			$args = wp_parse_args( $args, array(

			) );

			ob_start();
			?>

			<div class="sllv-video -type_<?php echo $args['provider']; ?>" data-provider="<?php echo $args['provider']; ?>" data-video="<?php echo esc_attr( $args['id'] ); ?>">
				<a class="sllv-video__link" href="<?php echo esc_url( $args['url'] ); ?>" rel="external noopener" target="_blank">
					<img class="sllv-video__media" src="<?php echo $args['thumbnail']; ?>" alt="<?php echo esc_attr( $args['title'] ); ?>">
				</a>
				<button class="sllv-video__button" type="button" aria-label="<?php _e( 'Play Video', 'simple-lazy-load-videos' ); ?>"><?php echo $args['play']; ?></button>
			</div>

			<?php
			$return = ob_get_clean();
			return $return;
		}

	}
}
