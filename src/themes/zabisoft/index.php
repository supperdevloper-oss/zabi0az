<?php
/**
 * Main template file — used for the blog index and as the ultimate fallback.
 *
 * @package ZabiSoft
 */

get_header();

if ( ! is_front_page() ) {
	zabisoft_breadcrumbs();
	?>
	<header class="zs-page-header">
		<h1>
			<?php
			if ( is_home() ) {
				echo esc_html( get_the_title( get_option( 'page_for_posts' ) ) ? get_the_title( get_option( 'page_for_posts' ) ) : __( 'Latest articles', 'zabisoft' ) );
			} else {
				the_archive_title();
			}
			?>
		</h1>
		<?php
		$zs_description = get_the_archive_description();
		if ( $zs_description ) {
			echo wp_kses_post( $zs_description );
		}
		?>
	</header>
	<?php
}

get_template_part( 'template-parts/loop' );

get_footer();
