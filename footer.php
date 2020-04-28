<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package RefPress
 */
?>
	</div><!-- #content -->

</div><!-- #page -->

<footer id="wporg-footer" class="devhub-wrap" role="contentinfo">

	<div class="wrapper site-footer">

		<?php dynamic_sidebar( 'site_footer' ); ?>

	</div> <!-- .site-footer -->

	<div class="divider"></div>

	<div class="copyright">
		<?php echo get_theme_mod( 'footer_copyright_text', sprintf( __( '&copy; %s %s | All Rights Reserved', 'refpress' ), '<span class="copyright-year">' . date_i18n( 'Y' ) . '</span>', '<span class="copyright-holder">' . get_bloginfo( 'name' ) . '</span>' ) ); ?>
	</div> <!-- .copyright -->

</footer>

<script src="https://s.w.org/style/js/navigation.min.js?20190128"></script>

<script type="text/javascript" src="https://gravatar.com/js/gprofiles.js"></script>

<?php wp_footer(); ?>

</body></html>
