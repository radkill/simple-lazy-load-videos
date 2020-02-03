<?php
/**
 * Front styles
 */
if ( ! function_exists( 'sllv_styles' ) ) {
	add_action( 'wp_enqueue_scripts', 'sllv_styles' );
	add_action( 'admin_enqueue_scripts', 'sllv_styles' );
	function sllv_styles() {
		if ( file_exists( SLLV_PATH . 'assets/css/main.min.css' ) ) {
			wp_enqueue_style( 'sllv-css-main', SLLV_URL . 'assets/css/main.min.css', false, filemtime( SLLV_PATH . 'assets/css/main.min.css' ) );
		}
	}
}


/**
 * Editor styles
 */
add_action( 'after_setup_theme', 'sllv_editor_style' );
function sllv_editor_style() {
  if ( file_exists( SLLV_PATH . 'assets/css/main.min.css' ) ) {
    add_editor_style( SLLV_URL . 'assets/css/main.min.css' );
  }
}
