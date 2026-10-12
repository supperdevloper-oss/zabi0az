<?php
/**
 * The loop used by index, archive, search and the front page.
 *
 * @package ZabiSoft
 */

if ( have_posts() ) :

	echo '<div class="zs-post-list">';

	while ( have_posts() ) :
		the_post();

		if ( 'software' === get_post_type() ) {
			get_template_part( 'template-parts/content', 'software' );
		} else {
			get_template_part( 'template-parts/content', 'post' );
		}

	endwhile;

	echo '</div>';

	zabisoft_pagination();

else :

	get_template_part( 'template-parts/content', 'none' );

endif;
