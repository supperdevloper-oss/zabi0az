<?php
/**
 * Single blog post.
 *
 * @package ZabiSoft
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'zs-entry' ); ?>>
		<header class="zs-entry__header">
			<?php zabisoft_breadcrumbs(); ?>

			<h1 class="zs-entry__title"><?php the_title(); ?></h1>

			<ul class="zs-entry__meta">
				<li>
					<?php zabisoft_icon( 'calendar' ); ?>
					<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
				</li>
				<li>
					<?php zabisoft_icon( 'user' ); ?>
					<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a>
				</li>
				<?php if ( has_category() ) : ?>
					<li>
						<?php zabisoft_icon( 'folder' ); ?>
						<?php the_category( ', ' ); ?>
					</li>
				<?php endif; ?>
				<li>
					<?php zabisoft_icon( 'clock' ); ?>
					<?php echo esc_html( zabisoft_reading_time() ); ?>
				</li>
				<?php if ( comments_open() ) : ?>
					<li>
						<?php zabisoft_icon( 'comment' ); ?>
						<a href="#comments"><?php echo esc_html( sprintf( _n( '%s comment', '%s comments', get_comments_number(), 'zabisoft' ), number_format_i18n( get_comments_number() ) ) ); ?></a>
					</li>
				<?php endif; ?>
			</ul>
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
		get_template_part( 'template-parts/author-box' );
		get_template_part( 'template-parts/related' );

		get_template_part( 'template-parts/post-navigation' );

		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>
	</article>
	<?php
endwhile;

get_footer();
