<?php
/**
 * Reference Template: Source Information
 *
 * @package RefPress
 * @subpackage Reference
 */

namespace DevHub;

global $refpress_options;

if ( ! $refpress_options->source_code ) {
	return;
}

$source_file = get_source_file();
if ( ! empty( $source_file ) ) :
	?>
	<hr />
	<section class="source-content">
		<h3><?php _e( 'Source', 'refpress' ); ?></h3>
		<p>
			<?php printf( __( 'File: %s', 'refpress' ),
				'<a href="' . esc_url( get_source_file_archive_link( $source_file ) ) . '">' . esc_html( $source_file ) . '</a>'
			); ?>
		</p>

		<?php if ( post_type_has_source_code() ) : ?>
			<div class="source-code-container">
				<pre class="brush: php; toolbar: false; first-line: <?php echo esc_attr( get_post_meta( get_the_ID(), '_wp-parser_line_num', true ) ); ?>"><?php echo htmlentities( get_source_code() ); ?></pre>
			</div>
			<p class="source-code-links">
				<span>
					<a href="#" class="show-complete-source"><?php _e( 'Expand full source code', 'refpress' ); ?></a>
					<a href="#" class="less-complete-source"><?php _e( 'Collapse full source code', 'refpress' ); ?></a>
				</span>
			</p>
		<?php endif; ?>
	</section>
<?php endif; ?>
