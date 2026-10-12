<?php
/**
 * Software archive: category shortcuts and the full list of entries.
 *
 * The list markup is the same as on the front page and on the category pages,
 * so a visitor always sees the same card: thumbnail, title, meta line, short
 * description and a read more button.
 *
 * @package ZabiSoft
 */

get_header();

zabisoft_breadcrumbs();

$zs_count = (int) $GLOBALS['wp_query']->found_posts;
?>

<header class="zs-page-header">
	<h1>
		<?php
		if ( is_post_type_archive( 'software' ) ) {
			esc_html_e( 'All software', 'zabisoft' );
		} else {
			the_archive_title();
		}
		?>
	</h1>

	<?php if ( $zs_count ) : ?>
		<p>
			<?php
			printf(
				/* translators: %s: number of entries. */
				esc_html( _n( '%s program with full details, screenshots and checked mirrors.', '%s programs with full details, screenshots and checked mirrors.', $zs_count, 'zabisoft' ) ),
				esc_html( number_format_i18n( $zs_count ) )
			);
			?>
		</p>
	<?php endif; ?>
</header>

<?php
// Quick links to the categories, so visitors can narrow the list down quickly.
$zs_terms = get_terms(
	array(
		'taxonomy'   => 'software_category',
		'hide_empty' => true,
		'number'     => 12,
		'parent'     => 0,
	)
);

if ( ! is_wp_error( $zs_terms ) && $zs_terms ) :
	?>
	<nav class="zs-cat-strip zs-cat-strip--archive" aria-label="<?php esc_attr_e( 'Software categories', 'zabisoft' ); ?>">
		<?php foreach ( $zs_terms as $zs_term ) : ?>
			<a class="zs-cat-strip__item" href="<?php echo esc_url( get_term_link( $zs_term ) ); ?>">
				<span><?php echo esc_html( $zs_term->name ); ?></span>
				<span class="count"><?php echo esc_html( number_format_i18n( $zs_term->count ) ); ?></span>
			</a>
		<?php endforeach; ?>
	</nav>
	<?php
endif;

get_template_part( 'template-parts/loop' );

get_footer();
