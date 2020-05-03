<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package RefPress
 */

$GLOBALS['pagetitle'] = wp_get_document_title();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link rel="profile" href="http://gmpg.org/xfn/11">

		<?php wp_head(); ?>
	</head>

	<body id="wordpress-org" <?php body_class(); ?>>
		<header id="masthead" class="site-header<?php if ( is_front_page() ) { echo ' home'; } ?>" role="banner">
			<?php if ( function_exists( 'wporg_is_handbook' ) && wporg_is_handbook() ) : ?>
				<a href="#" id="secondary-toggle" onclick="return false;"><strong><?php _e( 'Menu', 'refpress' ); ?></strong></a>
			<?php endif; ?>
			<div class="site-branding">
				<?php
				if ( is_front_page() ) { ?>

					<h1 class="site-title">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
							<?php bloginfo( 'name' ); ?>
						</a>
					</h1>

					<p class="site-description"><?php bloginfo( 'description' ); ?></p>

				<?php } else { ?>

					<span class="h1 site-title">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
							<?php bloginfo( 'name' ); ?>
						</a>
					</span>

				<?php } ?>

				<nav id="site-navigation" class="main-navigation" role="navigation">
					<button class="menu-toggle dashicons dashicons-arrow-down-alt2" aria-controls="primary-menu" aria-expanded="false" aria-label="<?php esc_attr_e( 'Primary Menu', 'refpress' ); ?>"></button>
					<?php
					$active_menu = is_post_type_archive( 'command' ) || is_singular( 'command' ) ? 'devhub-cli-menu' : 'devhub-menu';
					wp_nav_menu( array(
						'theme_location'  => $active_menu,
						'container_class' => 'menu-container',
						'container_id'    => 'primary-menu',
						'fallback_cb'     => function() {
							wp_page_menu( array(
								'menu_class'   => 'menu-container',
							) );
						},
					) );
					?>
				</nav>
			</div>
		</header><!-- #masthead -->

		<div id="page" class="hfeed site devhub-wrap">
			<a href="#main" class="screen-reader-text"><?php _e( 'Skip to content', 'refpress' ); ?></a>

			<?php do_action( 'before' ); ?>
			<?php
			if ( DevHub\should_show_search_bar() ) : ?>
				<div id="inner-search">
					<?php get_search_form(); ?>
					<div id="inner-search-icon-container">
						<div id="inner-search-icon">
							<div class="dashicons dashicons-search"><span class="screen-reader-text"><?php _e( 'Search', 'refpress' ); ?></span></div>
						</div>
					</div>
				</div>

			<?php endif; ?>
			<div id="content" class="site-content">

