<?php
/**
 * Template Name: About page
 * Template Description: About page with highlight cards, statistics and free content area.
 *
 * @package ZabiSoft
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'zs-entry zs-entry--page zs-entry--about' ); ?>>
		<header class="zs-entry__header">
			<?php zabisoft_breadcrumbs(); ?>

			<h1 class="zs-entry__title"><?php the_title(); ?></h1>

			<?php if ( has_excerpt() ) : ?>
				<p class="zs-entry__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</header>

		<div class="zs-entry__content entry-content">
			<?php the_content(); ?>
		</div>

		<div class="zs-about-grid">
			<div class="zs-about-card">
				<h3><?php esc_html_e( 'What we publish', 'zabisoft' ); ?></h3>
				<p><?php esc_html_e( 'Program pages built around facts: publisher, version, file size, supported systems, system requirements and installation steps — plus tutorials for common problems.', 'zabisoft' ); ?></p>
			</div>
			<div class="zs-about-card">
				<h3><?php esc_html_e( 'How we link', 'zabisoft' ); ?></h3>
				<p><?php esc_html_e( 'Every download button opens the official publisher page or a clearly named mirror. The destination host is always printed next to the button so nothing is hidden.', 'zabisoft' ); ?></p>
			</div>
			<div class="zs-about-card">
				<h3><?php esc_html_e( 'Copyright', 'zabisoft' ); ?></h3>
				<p><?php esc_html_e( 'We do not redistribute installers we have no right to share. Trademarks and product names belong to their owners and are used for identification only.', 'zabisoft' ); ?></p>
			</div>
		</div>

		<ul class="zs-stats">
			<?php
			$zs_software = post_type_exists( 'software' ) ? wp_count_posts( 'software' ) : null;
			$zs_posts    = wp_count_posts( 'post' );
			$zs_cats     = wp_count_terms( array( 'taxonomy' => 'category', 'hide_empty' => true ) );
			?>
			<li>
				<span class="num"><?php echo esc_html( $zs_software && isset( $zs_software->publish ) ? number_format_i18n( (int) $zs_software->publish ) : '0' ); ?></span>
				<span class="lbl"><?php esc_html_e( 'Program pages', 'zabisoft' ); ?></span>
			</li>
			<li>
				<span class="num"><?php echo esc_html( isset( $zs_posts->publish ) ? number_format_i18n( (int) $zs_posts->publish ) : '0' ); ?></span>
				<span class="lbl"><?php esc_html_e( 'Articles', 'zabisoft' ); ?></span>
			</li>
			<li>
				<span class="num"><?php echo esc_html( is_wp_error( $zs_cats ) ? '0' : number_format_i18n( (int) $zs_cats ) ); ?></span>
				<span class="lbl"><?php esc_html_e( 'Categories', 'zabisoft' ); ?></span>
			</li>
		</ul>

		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>
	</article>
	<?php
endwhile;

get_footer();
