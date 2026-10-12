<?php
/**
 * Archive template (categories, tags, dates, authors, custom taxonomies).
 *
 * @package ZabiSoft
 */

get_header();

zabisoft_breadcrumbs();
?>

<header class="zs-page-header">
	<h1><?php the_archive_title(); ?></h1>
	<?php
	$zs_description = get_the_archive_description();

	if ( $zs_description ) {
		echo wp_kses_post( $zs_description );
	} else {
		$zs_count = (int) $GLOBALS['wp_query']->found_posts;

		if ( $zs_count ) {
			printf(
				'<p>%s</p>',
				esc_html(
					sprintf(
						/* translators: %s: number of entries. */
						_n( '%s entry in this section.', '%s entries in this section.', $zs_count, 'zabisoft' ),
						number_format_i18n( $zs_count )
					)
				)
			);
		}
	}
	?>
</header>

<?php
get_template_part( 'template-parts/loop' );

get_footer();
