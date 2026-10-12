<?php
/**
 * Author box.
 *
 * @package ZabiSoft
 */

if ( ! get_the_author_meta( 'description' ) && ! is_singular() ) {
	return;
}
?>
<section class="zs-author-box">
	<?php echo get_avatar( get_the_author_meta( 'ID' ), 96, '', '', array( 'loading' => 'lazy' ) ); ?>

	<div class="zs-author-box__body">
		<h2 class="zs-author-box__name">
			<?php
			printf(
				/* translators: %s: author name. */
				esc_html__( 'About %s', 'zabisoft' ),
				esc_html( get_the_author() )
			);
			?>
		</h2>

		<?php if ( get_the_author_meta( 'description' ) ) : ?>
			<p><?php echo esc_html( get_the_author_meta( 'description' ) ); ?></p>
		<?php else : ?>
			<p>
				<?php
				printf(
					/* translators: %s: link to the author archive. */
					esc_html__( 'Read every article written by this author on the %s page.', 'zabisoft' ),
					'<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html__( 'author archive', 'zabisoft' ) . '</a>'
				);
				?>
			</p>
		<?php endif; ?>

		<p class="zs-author-box__links">
			<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php esc_html_e( 'All posts by this author', 'zabisoft' ); ?></a>
		</p>
	</div>
</section>
