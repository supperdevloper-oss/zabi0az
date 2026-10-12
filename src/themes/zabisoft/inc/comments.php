<?php
/**
 * Comment rendering.
 *
 * @package ZabiSoft
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'zabisoft_comment_template' ) ) {
	/**
	 * Render a single comment.
	 *
	 * @param WP_Comment $comment Comment object.
	 * @param array      $args    Comment args.
	 * @param int        $depth   Nesting depth.
	 * @return void
	 */
	function zabisoft_comment_template( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		?>
		<li id="comment-<?php comment_ID(); ?>" <?php comment_class( 'zs-comment' ); ?>>
			<article class="zs-comment__body">
				<?php
				echo get_avatar(
					$comment,
					isset( $args['avatar_size'] ) ? (int) $args['avatar_size'] : 64,
					'',
					'',
					array(
						'class'   => 'zs-comment__avatar',
						'loading' => 'lazy',
					)
				);
				?>

				<header class="zs-comment__meta">
					<span class="zs-comment__author"><?php echo esc_html( get_comment_author( $comment ) ); ?></span>
					<time datetime="<?php echo esc_attr( get_comment_time( DATE_W3C ) ); ?>">
						<?php
						printf(
							/* translators: %s: human readable time difference. */
							esc_html__( '%s ago', 'zabisoft' ),
							esc_html( human_time_diff( (int) get_comment_time( 'U' ), (int) current_time( 'timestamp' ) ) )
						);
						?>
					</time>
					<?php if ( '0' === $comment->comment_approved ) : ?>
						<em><?php esc_html_e( 'Your comment is awaiting moderation.', 'zabisoft' ); ?></em>
					<?php endif; ?>
				</header>

				<div class="zs-comment__content">
					<?php comment_text(); ?>
				</div>

				<footer class="zs-comment__actions">
					<?php
					comment_reply_link(
						array_merge(
							(array) $args,
							array(
								'depth'      => $depth,
								'max_depth'  => isset( $args['max_depth'] ) ? $args['max_depth'] : 5,
								'reply_text' => __( 'Reply', 'zabisoft' ),
							)
						)
					);

					edit_comment_link( __( 'Edit', 'zabisoft' ) );
					?>
				</footer>
			</article>
		<?php
		// Closing </li> is printed by WordPress.
	}
}

/**
 * Nicer comment form field order for this theme.
 *
 * @param array $fields Default fields.
 * @return array
 */
function zabisoft_comment_form_fields( $fields ) {
	if ( isset( $fields['author'] ) ) {
		$fields['author'] = '<p class="comment-form-author"><label for="author">' . esc_html__( 'Name', 'zabisoft' ) . ' <span class="required">*</span></label><input id="author" name="author" type="text" required></p>';
	}

	if ( isset( $fields['email'] ) ) {
		$fields['email'] = '<p class="comment-form-email"><label for="email">' . esc_html__( 'Email', 'zabisoft' ) . ' <span class="required">*</span></label><input id="email" name="email" type="email" required></p>';
	}

	if ( isset( $fields['url'] ) ) {
		$fields['url'] = '<p class="comment-form-url"><label for="url">' . esc_html__( 'Website', 'zabisoft' ) . '</label><input id="url" name="url" type="url"></p>';
	}

	return $fields;
}
add_filter( 'comment_form_default_fields', 'zabisoft_comment_form_fields' );
