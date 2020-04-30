<?php
/**
 * Override core functions as the only way to change some stuff.
 *
 * @package RefPress
 * @author  ArThemes
 * @since   1.0.0
 */

/**
 * Outputs the voting markup for user contributed note.
 *
 * @overrides DevHub_User_Contributed_Notes_Voting::show_voting()
 * @see DevHub_User_Contributed_Notes_Voting::show_voting()
 *
 * @param int $comment_id The comment ID, or empty to use current comment.
 */
function refpress_compat_show_voting( $comment_id = '') {
	if ( ! $comment_id ) {
		global $comment;
		$comment_id = $comment->comment_ID;
	}

	$can_vote     = DevHub_User_Contributed_Notes_Voting::user_can_vote( get_current_user_id(), $comment_id );
	$user_note    = DevHub_User_Contributed_Notes_Voting::is_current_user_note( $comment_id );
	$logged_in    = is_user_logged_in();
	$comment_link = get_comment_link( $comment_id );
	$nonce        = wp_create_nonce( 'user-note-vote-' . $comment_id );
	$disabled_str = __( 'Voting for this note is disabled', 'wporg' );
	$cancel_str   = __( 'Click to cancel your vote', 'wporg' );
	$log_in_str   = __( 'You must log in to vote on the helpfulness of this note', 'wporg' );
	$log_in_url   = wp_login_url( $comment_link );

	if ( ! $can_vote && $user_note ) {
		$disabled_str = __( 'Voting for your own note is disabled', 'wporg' );
	}

	echo '<div class="user-note-voting" data-nonce="' . esc_attr( $nonce ) . '">';

	// Up vote link
	$user_upvoted = DevHub_User_Contributed_Notes_Voting::has_user_upvoted_comment( $comment_id );
	if ( $can_vote ) {
		$cancel = $user_upvoted ? '. ' . $cancel_str . '.' : '';
		$title = $user_upvoted ?
			__( 'You have voted to indicate this note was helpful', 'wporg' ) . $cancel :
			__( 'Vote up if this note was helpful', 'wporg' );
		$tag = 'a';
	} else {
		$title = ! $logged_in ? $log_in_str : $disabled_str;
		$tag = $logged_in ? 'span' : 'a';
	}
	echo "<{$tag} "
		. 'class="user-note-voting-up' . ( $user_upvoted ? ' user-voted' : '' )
		. '" title="' . esc_attr( $title )
		. '" data-id="' . esc_attr( $comment_id )
		. '" data-vote="up';
	if ( 'a' === $tag ) {
		$up_url = $logged_in ?
			add_query_arg( array( '_wpnonce' => $nonce , 'comment' => $comment_id, 'vote' => 'up' ), $comment_link ) :
			$log_in_url;
		echo '" href="' . esc_url( $up_url );
	}
	echo '">';
	echo '<span class="dashicons dashicons-arrow-up" aria-hidden="true"></span>';
	echo '<span class="screen-reader-text">' . $title .  '</span>';
	echo "</{$tag}>";

	// Total count
	// Don't indicate a like percentage if no one voted.
	$title = ( 0 == DevHub_User_Contributed_Notes_Voting::count_votes( $comment_id, 'total' ) ) ?
		'' :
		sprintf( __( '%s like this', 'wporg' ), DevHub_User_Contributed_Notes_Voting::count_votes( $comment_id, 'like_percentage' ) . '%' );
	$class = '';
	echo '<span '
		. 'class="user-note-voting-count ' . esc_attr( $class ) . '" '
		. 'title="' . esc_attr( $title ) . '">'
		. '<span class="screen-reader-text">' . __( 'Vote results for this note: ', 'wporg' ) .  '</span>'
		. DevHub_User_Contributed_Notes_Voting::count_votes( $comment_id, 'difference' )
		. '</span>';

	// Down vote link
	$user_downvoted = ( $user_upvoted ? false : DevHub_User_Contributed_Notes_Voting::has_user_downvoted_comment( $comment_id ) );
	if ( $can_vote ) {
		$cancel = $user_downvoted ? '. ' . $cancel_str . '.' : '';
		$title = $user_downvoted ?
			__( 'You have voted to indicate this note was not helpful', 'wporg' ) . $cancel :
			__( 'Vote down if this note was not helpful', 'wporg' );
		$tag = 'a';
	} else {
		$title = ! $logged_in ? $log_in_str : $disabled_str;
		$tag = $logged_in ? 'span' : 'a';
	}
	echo "<{$tag} "
		. 'class="user-note-voting-down' . ( $user_downvoted ? ' user-voted' : '' )
		. '" title="' . esc_attr( $title )
		. '" data-id="' . esc_attr( $comment_id )
		. '" data-vote="down';
	if ( 'a' === $tag ) {
		$down_url = $logged_in ?
			add_query_arg( array( '_wpnonce' => $nonce , 'comment' => $comment_id, 'vote' => 'down' ), $comment_link ) :
			$log_in_url;
		echo '" href="' . esc_url( $down_url );
	}
	echo '">';
	echo '<span class="dashicons dashicons-arrow-down" aria-hidden="true"></span>';
	echo '<span class="screen-reader-text">' . $title .  '</span>';
	echo "</{$tag}>";

	echo '</div>';
}

add_action( 'init', function() {
	remove_action( 'wp_ajax_note_vote',  array( 'DevHub_User_Contributed_Notes_Voting', 'ajax_vote_submission' ) );
	add_action( 'wp_ajax_note_vote',  function() {
		check_ajax_referer( 'user-note-vote-' . $_POST['comment'], $_POST['_wpnonce'] );

		$_REQUEST['is_ajax'] = true;
		// If voting succeeded and resulted in a change, send back full replacement
		// markup.
		if ( DevHub_User_Contributed_Notes_Voting::vote_submission( false ) ) {
			refpress_compat_show_voting( (int) $_POST['comment'] );
			die();
		}
		die( 0 );
	} );
}, 99 );

/**
 * Capture an {@see wp_editor()} instance as the 'User Contributed Notes' feedback form.
 *
 * Uses output buffering to capture the editor instance.
 *
 * @param WP_Comment|false $comment Comment object or false. Default false.
 * @param string           $display Display the editor. Default 'show'.
 * @param bool             $edit    True if the editor used for editing a note. Default false.
 * @return string HTML output for the wp_editor-ized feedback form.
 */
function refpress_compat_wp_editor_feedback( $comment, $display = 'show', $edit = false ) {

	if ( ! ( isset( $comment->comment_ID ) && absint( $comment->comment_ID ) ) ) {
		return '';
	}

	$comment_id = absint( $comment->comment_ID );

	static $instance = 0;
	$instance++;

	$display       = ( 'hide' === $display ) ? ' style="display: none;"' : '';
	$parent        = $comment_id;
	$action        = site_url( '/wp-comments-post.php' );
	$title         = __( 'Add feedback to this note', 'wporg' );
	$button_text   = __( 'Add Feedback', 'wporg' );
	$post_id       = isset( $comment->comment_post_ID ) ? $comment->comment_post_ID : get_the_ID();
	$content       = '';
	$form_type     = '';
	$note_link     = '';
	$class         = '';

	if ( $edit ) {
		$content       = isset( $comment->comment_content ) ? $comment->comment_content : '';
		$title         = __( 'Edit feedback', 'wporg' );
		$form_type     = '-edit';
		$button_text   = __( 'Edit Note', 'wporg' );
		$post_url      = get_permalink( $post_id );
		$action        = $post_url ? $post_url . '#comment-' . $comment_id : '';
		$parent        = isset( $comment->comment_parent ) ? $comment->comment_parent : 0;
		$parent_author = \DevHub\get_note_author( $parent );
		$class         = ' edit-feedback-editor';

		if ( $parent && $post_url && $parent_author ) {
			$post_url  = $post_url . '#comment-' . $parent;
			$parent_note = sprintf( __( 'note %d', 'wporg' ), $parent );

			/* translators: 1: note, 2: note author name */
			$note_link = sprintf( __( '%1$s by %2$s', 'wporg' ), "<a href='{$post_url}'>{$parent_note}</a>", $parent_author );
		}
	}

	$allowed_tags = '';
	foreach ( array( '<strong>', '<em>', '<code>', '<a>' ) as $tag ) {
		$allowed_tags .= '<code>' . htmlentities( $tag ) . '</code>, ';
	}

	ob_start();
	echo "<div id='feedback-editor-{$comment_id}' class='feedback-editor{$class}'{$display}>\n";
	if ( ! $edit ) {
		echo "<p class='feedback-editor-title'>{$title}</p>\n";
	}

	echo "<form id='feedback-form-{$instance}{$form_type}' class='feedback-form' method='post' action='{$action}' name='feedback-form-{$instance}'>\n";
	echo DevHub_User_Submitted_Content::get_editor_rules( 'feedback', $note_link );
	wp_editor( $content, 'feedback-comment-' . $instance, array(
			'media_buttons' => false,
			'textarea_name' => 'comment',
			'textarea_rows' => 3,
			'quicktags'     => array(
				'buttons' => 'strong,em,link'
			),
			'editor_css'    => DevHub_User_Submitted_Content::get_editor_style(),
			'teeny'         => true,
			'tinymce'       => false,
		) );

	echo '<p><strong>' . __( 'Note', 'wporg' ) . '</strong>: ' . __( 'No newlines allowed', 'wporg' ) . '. ';
	printf( __( 'Allowed tags: %s', 'wporg' ), trim( $allowed_tags, ', ' ) ) . "</p>\n";
	echo "<p><input id='submit-{$instance}' class='submit' type='submit' value='{$button_text}' name='submit-{$instance}'>\n";
	echo "<input type='hidden' name='comment_post_ID' value='{$post_id}' id='comment_post_ID-{$instance}' />\n";
	echo "<input type='hidden' name='comment_parent' id='comment_parent-{$instance}' value='{$parent}' />\n";

	if ( $edit ) {
		echo DevHub_User_Submitted_Content::get_edit_fields( $comment_id, $instance );
	}

	echo "</p>\n</form>\n</div><!-- #feedback-editor-{$comment_id} -->\n";
	return ob_get_clean();
}

if ( ! function_exists( 'wporg_developer_user_note' ) ) :
	/**
	 * Template for user contributed notes.
	 *
	 * @param object $comment Comment object.
	 * @param array  $args    Arguments.
	 * @param int    $depth   Nested comment depth.
	 */
	function wporg_developer_user_note( $comment, $args, $depth ) {
		$GLOBALS['comment']       = $comment;
		$GLOBALS['comment_depth'] = $depth;

		static $note_number = 0;

		$approved       = ( 0 < (int) $comment->comment_approved ) ? true : false;
		$is_parent      = ( 0 === (int) $comment->comment_parent ) ? true : false;
		$is_voting      = class_exists( 'DevHub_User_Contributed_Notes_Voting' );
		$count          = $is_voting ? (int)  DevHub_User_Contributed_Notes_Voting::count_votes( $comment->comment_ID, 'difference' ) : 0;
		$curr_user_note = $is_voting ? (bool) DevHub_User_Contributed_Notes_Voting::is_current_user_note( $comment->comment_ID ) : false;
		$edited_note_id = isset( $args['updated_note'] ) ? $args['updated_note'] : 0;
		$is_edited_note = ( $edited_note_id === (int) $comment->comment_ID );
		$note_author    = get_comment_author_link( $comment );
		$can_edit_note  = \DevHub\can_user_edit_note( $comment->comment_ID );
		$has_edit_cap   = current_user_can( 'edit_comment', $comment->comment_ID );

		// CSS Classes
		$comment_class = array();

		if ( -1 > $count ) {
			$comment_class[] = 'bad-note';
		}

		if ( $curr_user_note ) {
			$comment_class[] = 'user-submitted-note';
		}

		if ( ! $approved ) {
			$comment_class[] = 'user-note-moderated';
		}

		$date = sprintf( _x( '%1$s ago', '%1$s = human-readable time difference', 'wporg' ),
			human_time_diff( get_comment_time( 'U' ),
			current_time( 'timestamp' ) )
		);
		?>
		<li id="comment-<?php comment_ID(); ?>" data-comment-id="<?php echo $comment->comment_ID;  ?>" <?php comment_class( implode( ' ', $comment_class ) ); ?>>
		<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">

		<?php if ( $is_parent ) : ?>
			<a href="#comment-content-<?php echo $comment->comment_ID; ?>" class="screen-reader-text"><?php printf( __( 'Skip to note %d content', 'wporg' ), ++ $note_number ); ?></a>
			<header class="comment-meta">

			<?php
			if ( $is_voting ) {
				refpress_compat_show_voting();
			}
			?>
				<div class="comment-author vcard">
					<span class="comment-author-attribution">
					<?php
					if ( 0 != $args['avatar_size'] ) {
						echo get_avatar( $comment, $args['avatar_size'] );
					}

					printf( __( 'Contributed by %s', 'wporg' ), sprintf( '<cite class="fn">%s</cite>', $note_author ) );
					?>

					</span>
					&mdash;
					<a class="comment-date" href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
						<time datetime="<?php comment_time( 'c' ); ?>">
						<?php echo $date; ?>
						</time>
					</a>

					<?php edit_comment_link( __( 'Edit', 'wporg' ), '<span class="edit-link">&mdash; ', '</span>' ); ?>
					<?php if ( ! $has_edit_cap && $can_edit_note ) : ?>
						&mdash; <span class="comment-author-edit-link">
							<!-- Front end edit comment link -->
							<a class="comment-edit-link" href="<?php echo site_url( "/reference/comment/edit/{$comment->comment_ID}" ); ?>"><?php _e( 'Edit', 'wporg' ); ?></a>
						</span>
					<?php endif; ?>
					<?php if ( $can_edit_note && $is_edited_note ) : ?>
						&mdash; <span class="comment-edited">
						<?php _e( 'edited', 'wporg' ); ?>
						</span>
					<?php endif; ?>
					<?php if ( ! $approved ) : ?>
						&mdash; <span class="comment-awaiting-moderation"><?php _e( 'awaiting moderation', 'wporg' ); ?></span>
					<?php endif; ?>
				</div>
			</header>
			<!-- .comment-metadata -->
		<?php endif; ?>

			<div class="comment-content" id="comment-content-<?php echo $comment->comment_ID; ?>">
			<?php
			if ( $is_parent ) {
				comment_text();
			} else {
				$text = get_comment_text()  . ' &mdash; ';
				$text .= sprintf( __( 'By %s', 'wporg' ), sprintf( '<cite class="fn">%s</cite>', $note_author ) ) . ' &mdash; ';
				$text .= ' <a class="comment-date" href="'. esc_url( get_comment_link( $comment->comment_ID ) ) . '">';
				$text .= '<time datetime="' . get_comment_time( 'c' ) . '">' . $date . '</time></a>';

				if ( $has_edit_cap ) {
					// WP admin edit comment link.
					$text .= ' &mdash; <a class="comment-edit-link" href="' . get_edit_comment_link( $comment->comment_ID ) .'">';
					$text .= __( 'Edit', 'wporg' ) . '</a>';
				} elseif ( $can_edit_note ) {
					// Front end edit comment link.
					$text .= ' &mdash; <a class="comment-edit-link" href="' . site_url( "/reference/comment/edit/{$comment->comment_ID}" ) . '">';
					$text .= __( 'Edit', 'wporg' ) . '</a>';
				}

				if ( $can_edit_note && $is_edited_note ) {
					$text .= ' &mdash; <span class="comment-edited">' . __( 'edited', 'wporg' ) . '</span>';
				}

				if ( ! $approved ) {
					$text .= ' &mdash; <span class="comment-awaiting-moderation">' . __( 'awaiting moderation', 'wporg' ) . '</span>';
				}

				echo apply_filters( 'comment_text', $text );
			}
			?>
			</div><!-- .comment-content -->

		<?php if ( ! $is_parent ) : ?>
		</article>
		</li>
		<?php endif; ?>
	<?php
	}
endif; // ends check for wporg_developer_user_note()

if ( ! function_exists( 'wporg_developer_list_notes' ) ) :
	/**
	 * List user contributed notes.
	 *
	 * @param array   $comments Array with comment objects.
	 * @param array   $args Comment display arguments.
	 */
	function wporg_developer_list_notes( $comments, $args ) {
		$is_user_content    = class_exists( 'DevHub_User_Submitted_Content' );
		$is_user_logged_in  = is_user_logged_in();
		$can_user_post_note = DevHub\can_user_post_note( true, get_the_ID() );
		$is_user_verified   = $is_user_logged_in && $can_user_post_note;

		$args['updated_note'] = 0;
		if ( isset( $_GET['updated-note'] ) && $_GET['updated-note'] ) {
			$args['updated_note'] = absint( $_GET['updated-note'] );
		}

		foreach ( $comments as $comment ) {

			$comment_id = $comment->comment_ID;

			// Display parent comment.
			wporg_developer_user_note( $comment, $args, 1 );

			/* Use hide-if-js class to hide the feedback section if Javascript is enabled.
			 * Users can display the section with Javascript.
			 */
			echo "<section id='feedback-{$comment_id}' class='feedback hide-if-js'>\n";

			// Display child comments.
			if ( ! empty( $comment->child_notes ) ) {

				echo "<h4 class='feedback-title'>" . __( 'Feedback', 'wporg' ) . "</h4>\n";
				echo "<ul class='children'>\n";
				foreach ( $comment->child_notes as $child_note ) {
					wporg_developer_user_note( $child_note, $args, 2, $comment->show_editor );
				}
				echo "</ul>\n";
			}

			// Add a feedback form for logged in users.
			if ( $is_user_content && $is_user_verified ) {
				/* Show the feedback editor if we're replying to a note and Javascript is disabled.
				 * If Javascript is enabled the editor is hidden (as normal) by the 'hide-if-js' class.
				 */
				$display = $comment->show_editor ? 'show' : 'hide';
				echo refpress_compat_wp_editor_feedback( $comment, $display );
			}
			echo "</section><!-- .feedback -->\n";

			// Feedback links to log in, add feedback or show feedback.
			echo "<footer class='feedback-links' >\n";
			if ( $can_user_post_note ) {
				$feedback_link = trailingslashit( get_permalink() ) . "?replytocom={$comment_id}#feedback-editor-{$comment_id}";
				$display       = '';
				if ( ! $is_user_logged_in ) {
					$class         = 'login';
					$feedback_text = __( 'Log in to add feedback', 'wporg' );
					$feedback_link = wp_login_url( $feedback_link );
				} else {
					$class         ='add';
					$feedback_text = __( 'Add feedback to this note', 'wporg' );

					/* Hide the feedback link if the current user is logged in and the
					 * feedback editor is displayed (because Javascript is disabled).
					 * If Javascript is enabled the editor is hidden and the feedback link is displayed (as normal).
					 */
					$display = $is_user_verified && $comment->show_editor ? ' style="display:none"' : '';
				}
				echo '<a role="button" class="feedback-' . $class . '" href="' . esc_url( $feedback_link ) . '"' . $display . ' rel="nofollow">' . $feedback_text . '</a>';
			}

			// close parent list item
			echo "</footer>\n</article><!-- .comment-body -->\n</li>\n";
		}
	}
endif;
