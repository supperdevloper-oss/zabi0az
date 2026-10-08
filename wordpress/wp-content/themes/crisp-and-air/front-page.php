<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
$hero_title = get_theme_mod( 'ca_hero_title', __( 'Air fryer recipes for every day.', 'crisp-and-air' ) );
$hero_copy  = get_theme_mod( 'ca_hero_description', __( 'Crisp, satisfying recipes with clear steps, useful timing, and ingredients you can find.', 'crisp-and-air' ) );
$hero_id    = absint( get_theme_mod( 'ca_hero_image' ) );
$hero_url   = $hero_id ? wp_get_attachment_image_url( $hero_id, 'ca-hero' ) : get_template_directory_uri() . '/assets/images/hero-crispy-chicken.jpg';
?>
<main id="main-content" class="site-main home-page">
	<section class="home-hero">
		<div class="wrap home-hero__grid">
			<div class="home-hero__copy">
				<p class="hero-kicker"><span class="hero-kicker__mark" aria-hidden="true">✳</span><?php esc_html_e( 'Air fryer cooking, made simple', 'crisp-and-air' ); ?></p>
				<h1><?php echo esc_html( $hero_title ); ?></h1>
				<p class="home-hero__description"><?php echo esc_html( $hero_copy ); ?></p>
				<?php ca_theme_search_form( 'ca-search-form--hero' ); ?>
				<div class="home-hero__prompt"><span><?php esc_html_e( 'A good place to start:', 'crisp-and-air' ); ?></span><a href="<?php echo esc_url( home_url( '/?s=chicken' ) ); ?>"><?php esc_html_e( 'chicken', 'crisp-and-air' ); ?></a><a href="<?php echo esc_url( home_url( '/?s=salmon' ) ); ?>"><?php esc_html_e( 'salmon', 'crisp-and-air' ); ?></a><a href="<?php echo esc_url( home_url( '/?s=potatoes' ) ); ?>"><?php esc_html_e( 'potatoes', 'crisp-and-air' ); ?></a></div>
				<div class="hero-promises"><span><span aria-hidden="true">✓</span><?php esc_html_e( 'Clear cooking steps', 'crisp-and-air' ); ?></span><span><span aria-hidden="true">✓</span><?php esc_html_e( 'Practical timing', 'crisp-and-air' ); ?></span></div>
			</div>
			<div class="home-hero__visual">
				<div class="home-hero__image-frame">
					<?php if ( $hero_id ) : ?><?php echo wp_get_attachment_image( $hero_id, 'ca-hero', false, array( 'class' => 'home-hero__image', 'alt' => '', 'loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by WordPress image API. ?><?php else : ?><img class="home-hero__image" src="<?php echo esc_url( $hero_url ); ?>" alt="" loading="eager" fetchpriority="high" decoding="async" width="1440" height="1050" /><?php endif; ?>
				</div>
				<div class="home-hero__image-note"><span aria-hidden="true">✦</span><span><?php esc_html_e( 'The little appliance with a lot of possibility.', 'crisp-and-air' ); ?></span></div>
				<div class="home-hero__seal" aria-hidden="true"><span>MAKE</span><strong>it</strong><span>CRISP</span></div>
			</div>
		</div>
		<div class="home-hero__bottom wrap"><span><?php esc_html_e( 'A recipe for every craving', 'crisp-and-air' ); ?></span><span class="home-hero__line"></span><span><?php esc_html_e( 'Made for real home kitchens', 'crisp-and-air' ); ?></span></div>
	</section>

	<section class="category-section section-block" id="categories">
		<div class="wrap">
			<div class="section-heading section-heading--row">
				<div><span class="section-heading__kicker"><?php esc_html_e( 'Find your next favorite', 'crisp-and-air' ); ?></span><h2><?php esc_html_e( 'What sounds good?', 'crisp-and-air' ); ?></h2><p><?php esc_html_e( 'Start with what you are craving, then make it crisp.', 'crisp-and-air' ); ?></p></div>
				<a class="text-link" href="<?php echo esc_url( home_url( '/categories/' ) ); ?>"><?php esc_html_e( 'All categories', 'crisp-and-air' ); ?> <span aria-hidden="true">→</span></a>
			</div>
			<div class="category-grid">
				<?php foreach ( ca_theme_category_terms( 8 ) as $index => $term ) : ?>
					<a class="category-card category-card--<?php echo esc_attr( ( $index % 4 ) + 1 ); ?>" href="<?php echo esc_url( get_category_link( $term ) ); ?>">
						<span class="category-card__icon" aria-hidden="true"><?php echo esc_html( ca_theme_category_icon( $term->slug ) ); ?></span>
						<span class="category-card__title"><?php echo esc_html( $term->name ); ?></span>
						<span class="category-card__arrow" aria-hidden="true">↗</span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php
	$featured = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 3,
			'meta_key'            => '_ca_featured',
			'meta_value'          => '1',
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'        => true,
		)
	);
	if ( $featured->have_posts() ) :
		?>
		<section class="featured-section section-block section-block--soft" id="featured">
			<div class="wrap">
				<div class="section-heading section-heading--row"><div><span class="section-heading__kicker"><?php esc_html_e( 'A few good places to begin', 'crisp-and-air' ); ?></span><h2><?php esc_html_e( 'Editor’s picks', 'crisp-and-air' ); ?></h2><p><?php esc_html_e( 'Hand-picked recipes to help you get the most from your air fryer.', 'crisp-and-air' ); ?></p></div><a class="text-link" href="<?php echo esc_url( home_url( '/recipes/' ) ); ?>"><?php esc_html_e( 'Browse every recipe', 'crisp-and-air' ); ?> <span aria-hidden="true">→</span></a></div>
				<div class="recipe-grid recipe-grid--three">
					<?php while ( $featured->have_posts() ) : $featured->the_post(); ca_theme_recipe_card( get_the_ID(), 'featured' ); endwhile; ?>
				</div>
			</div>
		</section>
		<?php
	endif;
	wp_reset_postdata();
	?>

	<section class="latest-section section-block" id="latest-recipes">
		<div class="wrap">
			<div class="section-heading section-heading--row"><div><span class="section-heading__kicker"><?php esc_html_e( 'Fresh from the kitchen', 'crisp-and-air' ); ?></span><h2><?php esc_html_e( 'Latest recipes', 'crisp-and-air' ); ?></h2><p><?php esc_html_e( 'New ideas for easy dinners, snacks, sides, and more.', 'crisp-and-air' ); ?></p></div><a class="text-link" href="<?php echo esc_url( home_url( '/recipes/' ) ); ?>"><?php esc_html_e( 'See all recipes', 'crisp-and-air' ); ?> <span aria-hidden="true">→</span></a></div>
			<?php
			$latest = new WP_Query(
				array(
					'post_type'           => 'post',
					'post_status'         => 'publish',
					'posts_per_page'      => 4,
					'orderby'             => 'date',
					'order'               => 'DESC',
					'ignore_sticky_posts' => true,
					'no_found_rows'        => true,
				)
			);
			if ( $latest->have_posts() ) :
				?>
				<div class="recipe-grid recipe-grid--four">
					<?php while ( $latest->have_posts() ) : $latest->the_post(); ca_theme_recipe_card( get_the_ID() ); endwhile; ?>
				</div>
			<?php else : ?>
				<div class="empty-library"><span class="empty-library__icon" aria-hidden="true">✳</span><div><h3><?php esc_html_e( 'Your recipe library starts here.', 'crisp-and-air' ); ?></h3><p><?php esc_html_e( 'Add a recipe in WordPress and the latest collection will update automatically.', 'crisp-and-air' ); ?></p></div><a class="button button--outline" href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>"><?php esc_html_e( 'Add your first recipe', 'crisp-and-air' ); ?></a></div>
			<?php endif; wp_reset_postdata(); ?>
		</div>
	</section>
	<div class="wrap"><?php ca_theme_ad_slot( 'ca-archive-infeed', __( 'Homepage in-feed advertisement', 'crisp-and-air' ) ); ?></div>

	<?php
	$popular = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 4,
			'meta_key'            => '_ca_popular',
			'meta_value'          => '1',
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'        => true,
		)
	);
	if ( $popular->have_posts() ) :
		?>
		<section class="popular-section section-block section-block--forest" id="popular-recipes">
			<div class="wrap">
				<div class="section-heading section-heading--row section-heading--light"><div><span class="section-heading__kicker"><?php esc_html_e( 'Hand-picked for a first look', 'crisp-and-air' ); ?></span><h2><?php esc_html_e( 'Popular recipes', 'crisp-and-air' ); ?></h2><p><?php esc_html_e( 'Reliable ideas worth keeping close.', 'crisp-and-air' ); ?></p></div><a class="text-link text-link--light" href="<?php echo esc_url( home_url( '/popular-recipes/' ) ); ?>"><?php esc_html_e( 'View popular recipes', 'crisp-and-air' ); ?> <span aria-hidden="true">→</span></a></div>
				<div class="recipe-grid recipe-grid--four recipe-grid--dark">
					<?php while ( $popular->have_posts() ) : $popular->the_post(); ca_theme_recipe_card( get_the_ID(), 'dark' ); endwhile; ?>
				</div>
			</div>
		</section>
		<?php
	endif;
	wp_reset_postdata();
	?>

	<section class="browse-section section-block" id="browse-categories">
		<div class="wrap browse-section__inner">
			<div class="browse-section__copy"><span class="section-heading__kicker"><?php esc_html_e( 'Simple ways to browse', 'crisp-and-air' ); ?></span><h2><?php esc_html_e( 'Good things are cooking.', 'crisp-and-air' ); ?></h2><p><?php esc_html_e( 'From quick weeknight mains to the crispiest sides, find the kind of recipe you need today.', 'crisp-and-air' ); ?></p><a class="button button--dark" href="<?php echo esc_url( home_url( '/categories/' ) ); ?>"><?php esc_html_e( 'Explore all categories', 'crisp-and-air' ); ?> <span aria-hidden="true">→</span></a></div>
			<div class="browse-illustration" aria-hidden="true"><div class="browse-illustration__plate"><span class="browse-illustration__food browse-illustration__food--one"></span><span class="browse-illustration__food browse-illustration__food--two"></span><span class="browse-illustration__food browse-illustration__food--three"></span><span class="browse-illustration__leaf"></span></div><span class="browse-illustration__spark browse-illustration__spark--one">✦</span><span class="browse-illustration__spark browse-illustration__spark--two">✳</span></div>
		</div>
	</section>

	<section class="newsletter-section" id="newsletter">
		<div class="wrap newsletter-section__inner">
			<div class="newsletter-section__icon" aria-hidden="true"><span>✉</span></div>
			<div class="newsletter-section__copy"><span class="section-heading__kicker"><?php esc_html_e( 'A note from our kitchen', 'crisp-and-air' ); ?></span><h2><?php echo esc_html( get_theme_mod( 'ca_newsletter_title', __( 'A little more crisp in your inbox.', 'crisp-and-air' ) ) ); ?></h2><p><?php echo esc_html( get_theme_mod( 'ca_newsletter_description', __( 'Fresh air-fryer inspiration and practical cooking notes, whenever you are ready.', 'crisp-and-air' ) ) ); ?></p></div>
			<div class="newsletter-section__form">
				<?php $newsletter_shortcode = trim( (string) get_theme_mod( 'ca_newsletter_shortcode', '' ) ); if ( $newsletter_shortcode ) : ?><?php echo do_shortcode( wp_kses_post( $newsletter_shortcode ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Only site-admin-configured form shortcode is rendered. ?><?php else : ?><p class="newsletter-setup-note"><?php esc_html_e( 'Connect your email provider’s sign-up form in Appearance → Customize before collecting subscribers.', 'crisp-and-air' ); ?></p><?php endif; ?>
			</div>
		</div>
	</section>
</main>
<?php get_footer(); ?>
