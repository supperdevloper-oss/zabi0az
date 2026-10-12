<?php
/**
 * Static page.
 *
 * @package ZabiSoft
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'zs-entry zs-entry--page' ); ?>>
		<header class="zs-entry__header">
			<?php zabisoft_breadcrumbs(); ?>

			<h1 class="zs-entry__title"><?php the_title(); ?></h1>

			<?php if ( has_excerpt() ) : ?>
				<p class="zs-entry__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="zs-entry__thumbnail">
				<?php the_post_thumbnail( 'zabisoft-wide', array( 'loading' => 'eager' ) ); ?>
			</figure>
		<?php endif; ?>

		<div class="zs-entry__content entry-content">
			<?php the_content(); ?>
			<?php
			wp_link_pages(
				array(
					'before' => '<nav class="zs-page-links">' . esc_html__( 'Pages:', 'zabisoft' ),
					'after'  => '</nav>',
				)
			);
			?>
		</div>

		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>
	</article>
	<?php
endwhile;

get_footer();
