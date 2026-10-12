<?php
/**
 * Search results.
 *
 * @package ZabiSoft
 */

get_header();

zabisoft_breadcrumbs();
?>

<header class="zs-page-header">
	<h1>
		<?php
		printf(
			/* translators: %s: search query. */
			esc_html__( 'Search results for “%s”', 'zabisoft' ),
			esc_html( get_search_query() )
		);
		?>
	</h1>
	<?php if ( have_posts() ) : ?>
		<p>
			<?php
			printf(
				/* translators: %s: number of results. */
				esc_html( _n( '%s result found.', '%s results found.', (int) $GLOBALS['wp_query']->found_posts, 'zabisoft' ) ),
				esc_html( number_format_i18n( (int) $GLOBALS['wp_query']->found_posts ) )
			);
			?>
		</p>
	<?php endif; ?>
	<div class="zs-search-again" style="max-width:520px;margin-top:14px;"><?php get_search_form(); ?></div>
</header>

<?php
get_template_part( 'template-parts/loop' );

get_footer();
