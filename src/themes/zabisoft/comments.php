<?php
/**
 * Comments template.
 *
 * @package ZabiSoft
 */

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="zs-comments">
	<?php if ( have_comments() ) : ?>
		<h2 class="zs-comments__title">
			<?php
			$zs_count = get_comments_number();

			printf(
				/* translators: %s: comment count. */
				esc_html( _n( '%s comment', '%s comments', $zs_count, 'zabisoft' ) ),
				esc_html( number_format_i18n( $zs_count ) )
			);
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 64,
					'callback'    => 'zabisoft_comment_template',
				)
			);
			?>
		</ol>

		<?php
		the_comments_pagination(
			array(
				'prev_text' => '&laquo; ' . esc_html__( 'Previous', 'zabisoft' ),
				'next_text' => esc_html__( 'Next', 'zabisoft' ) . ' &raquo;',
				'class'     => 'zs-pagination',
			)
		);
		?>

		<?php if ( ! comments_open() ) : ?>
			<p class="zs-notice"><?php esc_html_e( 'Comments are closed for this entry.', 'zabisoft' ); ?></p>
		<?php endif; ?>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'class_form'           => 'zs-comment-form',
			'title_reply'          => __( 'Leave a comment', 'zabisoft' ),
			'title_reply_before'   => '<h2 id="reply-title" class="comment-reply-title">',
			'title_reply_after'    => '</h2>',
			'comment_notes_before' => '<p class="comment-notes">' . esc_html__( 'Your email address will not be published. Required fields are marked with *.', 'zabisoft' ) . '</p>',
			'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Comment', 'zabisoft' ) . ' <span class="required">*</span></label><textarea id="comment" name="comment" rows="6" required></textarea></p>',
			'label_submit'         => __( 'Post comment', 'zabisoft' ),
			'class_submit'         => 'zs-btn',
		)
	);
	?>
</section>
