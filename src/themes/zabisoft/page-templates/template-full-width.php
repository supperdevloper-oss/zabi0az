<?php
/**
 * Template Name: Full width (no sidebar)
 * Template Description: Page without the sidebar, ideal for landing pages and long tables.
 *
 * @package ZabiSoft
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'zs-entry zs-entry--page zs-entry--full' ); ?>>
		<header class="zs-entry__header">
			<?php zabisoft_breadcrumbs(); ?>

			<h1 class="zs-entry__title"><?php the_title(); ?></h1>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="zs-entry__thumbnail">
				<?php the_post_thumbnail( 'full', array( 'loading' => 'eager' ) ); ?>
			</figure>
		<?php endif; ?>

		<div class="zs-entry__content entry-content"><?php the_content(); ?></div>
	</article>
	<?php
endwhile;

get_footer();
