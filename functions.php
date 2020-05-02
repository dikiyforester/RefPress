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

namespace RefPress;

define( 'RP_WPORG_DIR', dirname( __FILE__ ) . '/wporg-developer' );
define( 'RP_VERSION', '1.0.0' );

/**
 * Overrided core theme functions.
 *
 * Core Theme upgrade checklist:
 * 1. Find and override hardcoded usages of wordpress.org in URLs
 * 2. Check the templates (new or changed)
 * 3. Check overrided functions and templates
 * 4. Check out JS scripts and SCSS styles
 */
require dirname( __FILE__ ) . '/inc/core-overrides.php';

/**
 * Load wporg-developer theme as dependency.
 */
require RP_WPORG_DIR . '/functions.php';

/**
 * Load other dependencies.
 */
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require __DIR__ . '/vendor/autoload.php';
	require __DIR__ . '/vendor/phpdoc-parser/plugin.php';
}


/**
 * Alternate the core theme scripts and styles.
 */
function scripts_styles() {
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
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\scripts_styles', 20 );

/**
 * Alternate the core admin scripts and styles.
 */
function admin_scripts_styles() {

	// Minimize prod or show expanded in dev.
	$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	if ( wp_style_is( 'wporg-admin' ) ) {
		wp_dequeue_style( 'wporg-admin' );
		wp_enqueue_style( 'refpress-admin-style', get_template_directory_uri() . "/stylesheets/admin{$min}.css", array(), RP_VERSION );
	}

	wp_style_add_data( 'refpress-admin-style', 'suffix', $min );

}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\admin_scripts_styles', 20 );

/**
 * widgets_init function.
 *
 * @access public
 * @return void
 */
function register_sidebars() {
	register_sidebar( array(
		'name'          => __( 'Footer', 'refpress' ),
		'id'            => 'site_footer',
		'description'   => __( 'Site footer', 'refpress' ),
		'before_widget' => '<div id="%1$s" class="widget-footer widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', __NAMESPACE__ . '\\register_sidebars' );

/**
 * Handles adding/removing hooks as needed.
 */
add_action( 'init', function() {
	// Do not add default WordPress meta tags.
	remove_action( 'wp_head', array( 'DevHub_Head', 'output_head_tags' ), 2 );
}, 11 );

/**
 * Merge textdomains so included libs can use the theme translations.
 */
add_action( 'load_textdomain', function( $domain, $mofile ) {
	if ( 'refpress' === $domain && file_exists( $mofile ) ) {
		load_textdomain( 'wporg', $mofile );
		load_textdomain( 'breadcrumb-trail', $mofile );
		load_textdomain( 'wp-parser', $mofile );
	}
}, 10, 2 );

/**
 * Load theme textdomain.
 */
add_action( 'after_setup_theme', function () {
	load_theme_textdomain( 'refpress', get_template_directory() . '/languages' );
} );
