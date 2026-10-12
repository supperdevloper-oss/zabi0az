<?php
/**
 * Template Name: Contact page
 * Template Description: Contact page with a two column layout, contact details and a secure form.
 *
 * @package ZabiSoft
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'zs-entry zs-entry--page zs-entry--contact' ); ?>>
		<header class="zs-entry__header">
			<?php zabisoft_breadcrumbs(); ?>

			<h1 class="zs-entry__title"><?php the_title(); ?></h1>

			<?php if ( has_excerpt() ) : ?>
				<p class="zs-entry__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</header>

		<div class="zs-contact-grid">
			<div class="zs-entry__content entry-content">
				<?php
				if ( trim( get_the_content() ) ) {
					the_content();
				}
				?>

				<h2><?php esc_html_e( 'Send a message', 'zabisoft' ); ?></h2>
				<p class="zs-form-note">
					<?php esc_html_e( 'Fill in the form below and we will answer as soon as possible. Reports about broken links or wrong version numbers are especially welcome.', 'zabisoft' ); ?>
				</p>

				<?php echo do_shortcode( '[zabisoft_contact_form]' ); ?>
			</div>

			<aside class="zs-contact-card">
				<h3><?php esc_html_e( 'Other ways to reach us', 'zabisoft' ); ?></h3>

				<ul class="zs-contact-list">
					<li>
						<strong><?php esc_html_e( 'Email', 'zabisoft' ); ?></strong>
						<?php
						$zs_email = apply_filters( 'zabisoft_contact_email', get_option( 'admin_email' ) );
						printf( '<a href="%s">%s</a>', esc_url( 'mailto:' . antispambot( $zs_email ) ), esc_html( antispambot( $zs_email ) ) );
						?>
					</li>
					<li>
						<strong><?php esc_html_e( 'Response time', 'zabisoft' ); ?></strong>
						<?php esc_html_e( 'Usually within 1–2 business days.', 'zabisoft' ); ?>
					</li>
					<li>
						<strong><?php esc_html_e( 'Copyright / takedown requests', 'zabisoft' ); ?></strong>
						<?php esc_html_e( 'Please include the exact URL and proof of ownership so we can react quickly.', 'zabisoft' ); ?>
					</li>
				</ul>

				<p class="zs-form-note">
					<?php esc_html_e( 'We do not host any installer file on this website: every download button points to the official publisher or a mirror you must verify yourself.', 'zabisoft' ); ?>
				</p>
			</aside>
		</div>
	</article>
	<?php
endwhile;

get_footer();
