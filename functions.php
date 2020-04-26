<?php
/**
 * Theme functions file
 *
 * DO NOT MODIFY THIS FILE. Make a child theme instead: http://codex.wordpress.org/Child_Themes
 *
 * @package RefPress
 * @author  ArThemes
 * @since   1.0.0
 */

define( 'RP_WPORG_DIR', 'wporg-developer' );
define( 'RP_VERSION', '1.0.0' );

/**
 * Load wporg-developer theme as dependency.
 */
require __DIR__ . '/' . RP_WPORG_DIR . '/functions.php';

/**
 * Alternate the core theme scripts and styles.
 */
function refpress_scripts_styles() {
	wp_dequeue_style( 'awesomplete-css' );
	wp_dequeue_style( 'autocomplete-css' );
	wp_dequeue_style( 'wporg-developer-style' );
	wp_dequeue_style( 'wp-dev-sass-compiled' );

	// Minimize prod or show expanded in dev.
	$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_style( 'refpress-style', get_template_directory_uri() . "/stylesheets/main{$min}.css", array( 'wporg-developer-style' ), RP_VERSION );

	wp_style_add_data( 'refpress-style', 'rtl', 'replace' );
	wp_style_add_data( 'refpress-style', 'suffix', $min );

}
add_action( 'wp_enqueue_scripts', 'refpress_scripts_styles', 20 );

/**
 * Alternate the core admin scripts and styles.
 */
function refpress_admin_scripts_styles() {

	// Minimize prod or show expanded in dev.
	$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	if ( wp_style_is( 'wporg-admin' ) ) {
		wp_dequeue_style( 'wporg-admin' );
		wp_enqueue_style( 'refpress-admin-style', get_template_directory_uri() . "/stylesheets/admin{$min}.css", array(), RP_VERSION );
	}

	wp_style_add_data( 'refpress-admin-style', 'suffix', $min );

}
add_action( 'admin_enqueue_scripts', 'refpress_admin_scripts_styles', 20 );
