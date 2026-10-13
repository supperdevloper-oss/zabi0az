<?php
/**
 * Software entry in archive lists.
 *
 * @package ZabiSoft
 */

$zs_version = (string) get_post_meta( get_the_ID(), '_zs_version', true );
$zs_size    = (string) get_post_meta( get_the_ID(), '_zs_file_size', true );
$zs_os      = (string) get_post_meta( get_the_ID(), '_zs_req_os', true );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'zs-post zs-post--software' ); ?>>
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

			<?php if ( zabisoft_mod( 'listing_specs' ) && ( $zs_version || $zs_size || $zs_os ) ) : ?>
				<ul class="zs-specs-inline">
					<?php if ( $zs_version ) : ?>
						<li><span><?php esc_html_e( 'Version', 'zabisoft' ); ?></span> <?php echo esc_html( $zs_version ); ?></li>
					<?php endif; ?>
					<?php if ( $zs_size ) : ?>
						<li><span><?php esc_html_e( 'Size', 'zabisoft' ); ?></span> <?php echo esc_html( $zs_size ); ?></li>
					<?php endif; ?>
					<?php if ( $zs_os ) : ?>
						<li><span><?php esc_html_e( 'OS', 'zabisoft' ); ?></span> <?php echo esc_html( $zs_os ); ?></li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>

			<div class="zs-post__excerpt">
				<?php the_excerpt(); ?>
			</div>

			<a class="zs-readmore" href="<?php the_permalink(); ?>">
				<?php esc_html_e( 'Read More', 'zabisoft' ); ?>
			</a>
		</div>
	</div>
</article>
