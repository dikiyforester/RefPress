<?php
/**
 * Deafult settings
 *
 * @package RefPress
 * @author  ArThemes
 * @since   1.0.0
 */

namespace RefPress;

use WP_Parser;

/**
 * Deafult settings class
 */
class Settings extends \scbAdminPage {

	public $tab_sections;
	protected $table_classes = array();

	function __construct( $options = null ) {
		parent::__construct( false, $options );
		$this->table_classes = array_merge( $this->table_classes, array( 'form-table', esc_attr( $this->args['page_slug'] ) . '-table' ) );
		remove_filter( 'contextual_help', array( $this, '_contextual_help' ), 10, 2 );
	}

	function setup() {
		$this->textdomain = 'refpress';

		$this->args = array(
			'page_title'            => __( 'RefPress Settings', 'refpress' ),
			'menu_title'            => __( 'RefPress', 'refpress' ),
			'page_slug'             => 'refpress-settings',
			'toplevel'              => 'menu',
			'icon_url'              => 'dashicons-book-alt',
			'position'              => 3,
			'admin_action_priority' => 10,
		);

		$this->tab_sections = array(
			'general' => array(
				'fields' => array(
					array(
						'title' => __( 'Source Name', 'refpress' ),
						'name'  => 'source_name',
						'type'  => 'text',
						'desc'  => __( 'Source software name', 'refpress' ),
					),
					array(
						'title' => __( 'Source Description', 'refpress' ),
						'name'  => 'source_desc',
						'type'  => 'text',
						'desc'  => __( 'Short source software description', 'refpress' ),
					),
					array(
						'title' => __( 'Source Path', 'refpress' ),
						'name'  => 'source_path',
						'type'  => 'text',
						'desc'  => __( 'Source code path relative to the WordPress root directory', 'refpress' ),
					),
					array(
						'title' => __( 'Source Version', 'refpress' ),
						'name'  => 'source_ver',
						'type'  => 'text',
						'desc'  => __( 'Source software version tag', 'refpress' ),
					),
					array(
						'title' => __( 'Display Sorce Code', 'refpress' ),
						'name'  => 'source_code',
						'type'  => 'checkbox',
						'desc'  => __( 'Enable to display the source code to visitors.', 'refpress' ),
					),
				),
			),
		);

	}

	function form_handler() {
		if ( empty( $_POST['action'] ) ) {
			return false;
		}

		check_admin_referer( $this->nonce );

		foreach ( $this->tab_sections as &$section ) {

			if ( isset( $section['options'] ) && is_a( $section['options'], 'scbOptions' ) ) {
				$options =& $section['options'];
			} else {
				$options =& $this->options;
			}

			$to_update = \scbForms::validate_post_data( $section['fields'], null, $options->get() );

			$options->update( $to_update );
		}

		add_action( 'admin_notices', array( $this, 'admin_msg' ) );

		if ( ! empty( $_POST['generate-docs'] ) ) {
			if ( empty( $this->options->source_path ) || empty( $this->options->source_ver ) || ! class_exists( 'WP_Parser\Importer' ) ) {
				return;
			}

			$path = ABSPATH . $this->options->source_path;
			$path = realpath( $path );

			$is_file = is_file( $path );
			$files   = $is_file ? array( $path ) : WP_Parser\get_wp_files( $path );
			$path    = $is_file ? dirname( $path ) : $path;

			if ( $files instanceof \WP_Error ) {
				return;
			}

			$data = WP_Parser\parse_files( $files, $path );

			/**
			 * WordPress parser uses two-level packages hierarchy, like
			 * @package Some
			 * @subpackage Other
			 *
			 * Some packages use multilevel hierarchy, like
			 * @package Components\Payments\Admin
			 *
			 * Following code converts multilevel packages into WordPress' two-level packages:
			 * @package Components
			 * @subpackage Payments
			 */
			foreach ( $data as &$items ) {
				if ( ! empty( $items['file']['tags'] ) ) {
					$items['file']['tags'] = $this->add_subpackages( $items['file']['tags'] );
				}
				if ( ! empty( $items['classes'] ) ) {
					foreach ( $items['classes'] as &$class ) {
						if ( ! empty( $class['doc']['tags'] ) ) {
							$class['doc']['tags'] = $this->add_subpackages( $class['docs']['tags'] );
						}
					}
				}
				if ( ! empty( $items['functions'] ) ) {
					foreach ( $items['functions'] as &$func ) {
						if ( ! empty( $func['doc']['tags'] ) ) {
							$func['doc']['tags'] = $this->add_subpackages( $func['docs']['tags'] );
						}
					}
				}
				if ( ! empty( $items['hooks'] ) ) {
					foreach ( $items['hooks'] as &$hook ) {
						if ( ! empty( $hook['doc']['tags'] ) ) {
							$hook['doc']['tags'] = $this->add_subpackages( $hook['docs']['tags'] );
						}
					}
				}
			}

			$importer = new WP_Parser\Importer;
			$importer->import( $data );

			update_option( 'wp_parser_imported_wp_version', $this->options->source_ver );
		}
	}

	private function add_subpackages( &$tags ) {
		if ( ! is_array( $tags ) ) {
			return;
		}
		foreach ( $tags as &$tag ) {
			if ( 'package' === $tag['name'] && 0 !== strpos( $tag['content'], '\\' ) ) {
				$subackages = explode( '\\', $tag['content'] );
				// Make Components elements as the first-level items.
				if ( 'Components' === $subackages[0] ) {
					array_shift( $subackages );
				}
				$tag['content'] = array_shift( $subackages );
				if ( ! empty( $subackages) ) {
					$tags[] = array(
						'name'    => 'subpackage',
						'content' => array_shift( $subackages ),
					);
					// WordPress Parser can't process subpackage trees with more
					// than 2 leveles.
					/*foreach ( $subackages as $subackage ) {
						$tags[] = array(
							'name'    => 'subpackage',
							'content' => $subackage,
						);
					}*/
				}
			}
		}

		return $tags;
	}

	// A generic page header
	function page_header() {
		echo '<div class="wrap ' . esc_attr( $this->args['page_slug'] ) . '-page">' . "\n";
		echo html( 'h2', $this->args['page_title'] );
	}

	function page_content() {

		echo '<form method="post" action="">';
		echo '<input type="hidden" name="action" value="' . $this->pagehook . '" />';
		wp_nonce_field( $this->nonce );

		foreach ( $this->tab_sections as $section_id => $section ) {
			if ( isset( $section['title'] ) ) {
				echo html( 'h3 class="title"', $section['title'] );
			}

			if ( isset( $section['desc'] ) ) {
				echo html( 'p', $section['desc'] );
			}

			if ( isset( $section['renderer'] ) ) {
				call_user_func( $section['renderer'], $section, $section_id );
			} else {
				if ( isset( $section['options'] ) && is_a( $section['options'], 'scbOptions' ) ) {
					$formdata = $section['options'];
				} else {
					$formdata = $this->options;
				}
				$this->render_section( $section['fields'], $formdata->get(), $section_id );
			}
		}

		echo '<p class="submit"><input type="submit" class="button-primary" value="' . esc_attr__( 'Save Changes', 'refpress' ) . '" /></p>';

		echo '<b>wp_parser_root_import_dir:</b> ' . get_option( 'wp_parser_root_import_dir' ) . '<br />';
		echo '<b>wp_parser_last_import:</b> ' . date_i18n( 'Y-m-d H:i:s', get_option( 'wp_parser_last_import' ) ) . '<br />';
		echo '<b>wp_parser_imported_wp_version:</b> ' . get_option( 'wp_parser_imported_wp_version' ) . '<br />';
		echo $this->submit_button( __( 'Generate Docs', 'refpress' ), 'generate-docs' );
		echo '</form>';
	}

	private function render_section( $fields, $formdata = false, $section_id = '' ) {
		$output = '';

		foreach ( $fields as $field ) {
			$output .= $this->table_row( $this->before_rendering_field( $field ), $formdata );
		}

		echo $this->table_wrap( $output, $section_id );
	}

	public function table_wrap( $content, $section_id = '' ) {

		$table_classes = array_merge( $this->table_classes, array( "{$section_id}-section" ) );
		$args = array( 'class' => implode( ' ', $table_classes ) );

		return html( 'table', $args, $content );
	}

	public function table_row( $field, $formdata = false ) {

		if ( isset( $field['desc'] ) ) {
			// wrap textareas and regular-text fields in <p> tag
			// TODO: doesn't catch wrap_upload() instances for buttons
			if ( in_array( $field['type'], array( 'text', 'textarea', 'submit' ) ) ) {
				if ( ! isset( $field['extra']['class'] ) || strpos( $field['extra']['class'], 'small-text' ) === false ) {
					$field['desc'] = html( 'p class="description"', $field['desc'] );
				}
			}
		}

		$input = \scbForms::input( $field, $formdata );

		// wrap radio buttons in a <fieldset> tag following what WP also does
		if ( 'radio' == $field['type'] ) {
			$input = html( 'fieldset', $input );
		}

		return html( "tr",
			html( "th scope='row app-row'", html( 'label for="'.esc_attr( $field['title'] ).'"', $field['title'] ) ),
			html( "td", $input )
		);
	}

	/**
	 * Useful for adding dynamic descriptions to certain fields.
	 *
	 * @param array field arguments
	 * @return array modified field arguments
	 */
	protected function before_rendering_field( $field ) {
		return $field;
	}

}
