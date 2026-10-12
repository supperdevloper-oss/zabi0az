<?php
/**
 * Post entry in archive lists.
 *
 * @package ZabiSoft
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'zs-post' ); ?>>
	<?php
	$zs_comments = get_comments_number();
	if ( comments_open() && $zs_comments > 0 ) :
		?>
		<a class="zs-comment-bubble" href="<?php echo esc_url( get_comments_link() ); ?>">
			<?php echo esc_html( number_format_i18n( $zs_comments ) ); ?>
			<span class="screen-reader-text"><?php esc_html_e( 'comments', 'zabisoft' ); ?></span>
		</a>
	<?php endif; ?>

	<div class="zs-post__inner">
		<?php zabisoft_thumbnail( 'zabisoft-thumb', 'list' ); ?>

		<div class="zs-post__body">
			<h2 class="zs-post__title">
				<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h2>

			<?php zabisoft_entry_meta(); ?>

			<div class="zs-post__excerpt">
				<?php the_excerpt(); ?>
			</div>

			<a class="zs-readmore" href="<?php the_permalink(); ?>">
				<?php esc_html_e( 'Read more', 'zabisoft' ); ?>
			</a>
		</div>
	</div>
</article>
