<?php
/**
 * Single software entry.
 *
 * Layout order follows the classic software review page: overview, key
 * features, technical details, system requirements, screenshots, installation
 * instructions and finally the download mirrors.
 *
 * @package ZabiSoft
 */

get_header();

while ( have_posts() ) :
	the_post();

	$zs_meta     = zabisoft_get_software_meta();
	$zs_has_data = zabisoft_has_software_data();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'zs-entry zs-entry--software' ); ?>>
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
				<?php
				$zs_terms = get_the_terms( get_the_ID(), taxonomy_exists( 'software_category' ) ? 'software_category' : 'category' );
				if ( ! is_wp_error( $zs_terms ) && ! empty( $zs_terms ) ) :
					?>
					<li>
						<?php zabisoft_icon( 'folder' ); ?>
						<?php
						$zs_links = array();
						foreach ( $zs_terms as $zs_term ) {
							$zs_links[] = '<a href="' . esc_url( get_term_link( $zs_term ) ) . '">' . esc_html( $zs_term->name ) . '</a>';
						}
						echo wp_kses_post( implode( ', ', $zs_links ) );
						?>
					</li>
				<?php endif; ?>

				<?php if ( '' !== $zs_meta['_zs_version'] ) : ?>
					<li><?php zabisoft_icon( 'tag' ); ?><?php echo esc_html( sprintf( /* translators: %s: version. */ __( 'Version %s', 'zabisoft' ), $zs_meta['_zs_version'] ) ); ?></li>
				<?php endif; ?>

				<?php if ( comments_open() ) : ?>
					<li>
						<?php zabisoft_icon( 'comment' ); ?>
						<a href="#comments"><?php echo esc_html( sprintf( _n( '%s comment', '%s comments', get_comments_number(), 'zabisoft' ), number_format_i18n( get_comments_number() ) ) ); ?></a>
					</li>
				<?php endif; ?>
			</ul>
		</header>

		<?php get_template_part( 'template-parts/software/buttons' ); ?>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="zs-entry__thumbnail">
				<?php the_post_thumbnail( 'zabisoft-wide', array( 'loading' => 'eager' ) ); ?>
			</figure>
		<?php endif; ?>

		<?php
		$zs_toc = array();

		if ( zabisoft_mod( 'show_toc' ) ) {
			$zs_parsed = zabisoft_build_toc( get_the_content() );
			$zs_toc    = $zs_parsed['items'];

			get_template_part(
				'template-parts/software/toc',
				null,
				array(
					'items'   => $zs_toc,
					'section' => $zs_has_data,
				)
			);
		}
		?>

		<div class="zs-entry__content entry-content">
			<h2 id="zs-overview"><?php esc_html_e( 'Overview', 'zabisoft' ); ?></h2>
			<?php
			if ( zabisoft_mod( 'show_toc' ) ) {
				echo wp_kses_post( apply_filters( 'the_content', $zs_parsed['content'] ) );
			} else {
				the_content();
			}

			wp_link_pages(
				array(
					'before' => '<nav class="zs-page-links">' . esc_html__( 'Pages:', 'zabisoft' ),
					'after'  => '</nav>',
				)
			);
			?>
		</div>

		<?php
		if ( zabisoft_mod( 'show_screenshots' ) ) {
			get_template_part( 'template-parts/software/screenshots' );
		}

		if ( $zs_has_data ) {
			get_template_part( 'template-parts/software/specs' );

			if ( zabisoft_mod( 'show_requirements' ) ) {
				get_template_part( 'template-parts/software/requirements' );
			}

			get_template_part( 'template-parts/software/features' );

			if ( zabisoft_mod( 'show_install_steps' ) ) {
				get_template_part( 'template-parts/software/install-steps' );
			}
		}

		get_template_part( 'template-parts/software/downloads' );

		$zs_tags = get_the_term_list( get_the_ID(), 'software_tag', '<div class="zs-post__tags">', ' ', '</div>' );
		if ( $zs_tags && ! is_wp_error( $zs_tags ) ) {
			echo wp_kses_post( $zs_tags );
		}
		?>

		<?php
		get_template_part( 'template-parts/author-box' );
		get_template_part( 'template-parts/software/related' );
		get_template_part( 'template-parts/post-navigation' );

		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>
	</article>
	<?php
endwhile;

get_footer();
