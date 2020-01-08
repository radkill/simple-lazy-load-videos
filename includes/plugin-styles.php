<?php
/**
 * Styles
 */
add_action( 'wp_enqueue_scripts', 'sllv_styles' );
add_action( 'admin_enqueue_scripts', 'sllv_styles' );
function sllv_styles() {
	if ( SLLV_PATH . 'assets/css/main.min.css' ) {
		wp_enqueue_style( 'sllv-css-main', SLLV_URL . 'assets/css/main.min.css', false, filemtime( SLLV_PATH . 'assets/css/main.min.css' ) );
	}
}

add_editor_style( SLLV_URL . 'assets/css/main.min.css' );
