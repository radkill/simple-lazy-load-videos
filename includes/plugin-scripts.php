<?php
/**
 * Scripts
 */
if ( ! function_exists( 'sllv_scripts' ) ) {
	add_action( 'wp_enqueue_scripts', 'sllv_scripts' );
	function sllv_scripts() {
		if ( file_exists( SLLV_PATH . 'assets/js/scripts.js' ) ) {
			wp_enqueue_script( 'sllv-js-main', SLLV_URL . 'assets/js/scripts.js', array( 'jquery' ), filemtime( SLLV_PATH . 'assets/js/scripts.js' ), true );
		}
	}
}
