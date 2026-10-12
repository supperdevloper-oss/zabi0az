<?php
/**
 * Sidebar and footer widgets shipped with the theme.
 *
 * @package ZabiSoft
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Recent / popular posts widget with thumbnails.
 */
class ZabiSoft_Posts_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'zabisoft_posts',
			__( 'ZabiSoft: posts with thumbnails', 'zabisoft' ),
			array(
				'description' => __( 'Shows recent or popular entries with thumbnails and dates.', 'zabisoft' ),
				'classname'   => 'zs-widget-posts-widget',
			)
		);
	}

	/**
	 * Front end output.
	 *
	 * @param array $args     Sidebar args.
	 * @param array $instance Widget settings.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$title  = isset( $instance['title'] ) ? $instance['title'] : __( 'Recent posts', 'zabisoft' );
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$order  = isset( $instance['order'] ) ? $instance['order'] : 'recent';
		$ptype  = isset( $instance['post_type'] ) ? $instance['post_type'] : 'any';

		$query_args = array(
			'posts_per_page'      => max( 1, min( 12, $number ) ),
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		if ( 'any' !== $ptype && post_type_exists( $ptype ) ) {
			$query_args['post_type'] = $ptype;
		} elseif ( 'any' === $ptype ) {
			$types = array( 'post' );
			if ( post_type_exists( 'software' ) ) {
				$types[] = 'software';
			}
			$query_args['post_type'] = $types;
		}

		if ( 'popular' === $order ) {
			$query_args['orderby'] = 'comment_count';
			$query_args['order']   = 'DESC';
		}

		$posts = new WP_Query( $query_args );

		if ( ! $posts->have_posts() ) {
			return;
		}

		echo isset( $args['before_widget'] ) ? $args['before_widget'] : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( $title ) {
			echo ( isset( $args['before_title'] ) ? $args['before_title'] : '' ) . esc_html( $title ) . ( isset( $args['after_title'] ) ? $args['after_title'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '<ul class="zs-widget-posts">';

		while ( $posts->have_posts() ) {
			$posts->the_post();

			echo '<li>';
			if ( has_post_thumbnail() ) {
				echo '<a href="' . esc_url( get_permalink() ) . '" aria-hidden="true" tabindex="-1">';
				the_post_thumbnail(
					'thumbnail',
					array(
						'loading' => 'lazy',
						'alt'     => '',
					)
				);
				echo '</a>';
			}
			echo '<span class="zs-wp-base">';
			echo '<a class="zs-wp-title" href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a>';
			echo '<span class="zs-wp-meta">' . esc_html( get_the_date() ) . '</span>';
			echo '</span>';
			echo '</li>';
		}

		echo '</ul>';
		echo isset( $args['after_widget'] ) ? $args['after_widget'] : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		wp_reset_postdata();
	}

	/**
	 * Admin form.
	 *
	 * @param array $instance Widget settings.
	 * @return void
	 */
	public function form( $instance ) {
		$title     = isset( $instance['title'] ) ? $instance['title'] : __( 'Recent posts', 'zabisoft' );
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$order     = isset( $instance['order'] ) ? $instance['order'] : 'recent';
		$post_type = isset( $instance['post_type'] ) ? $instance['post_type'] : 'any';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'zabisoft' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of entries:', 'zabisoft' ); ?></label>
			<input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" min="1" max="12" value="<?php echo esc_attr( $number ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"><?php esc_html_e( 'Order:', 'zabisoft' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>">
				<option value="recent" <?php selected( $order, 'recent' ); ?>><?php esc_html_e( 'Newest first', 'zabisoft' ); ?></option>
				<option value="popular" <?php selected( $order, 'popular' ); ?>><?php esc_html_e( 'Most commented', 'zabisoft' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'post_type' ) ); ?>"><?php esc_html_e( 'Content type:', 'zabisoft' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_type' ) ); ?>">
				<option value="any" <?php selected( $post_type, 'any' ); ?>><?php esc_html_e( 'Everything', 'zabisoft' ); ?></option>
				<option value="software" <?php selected( $post_type, 'software' ); ?>><?php esc_html_e( 'Software only', 'zabisoft' ); ?></option>
				<option value="post" <?php selected( $post_type, 'post' ); ?>><?php esc_html_e( 'Blog posts only', 'zabisoft' ); ?></option>
			</select>
		</p>
		<?php
	}

	/**
	 * Save settings.
	 *
	 * @param array $new_instance New values.
	 * @param array $old_instance Old values.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		return array(
			'title'     => sanitize_text_field( $new_instance['title'] ?? '' ),
			'number'    => max( 1, min( 12, absint( $new_instance['number'] ?? 5 ) ) ),
			'order'     => in_array( ( $new_instance['order'] ?? 'recent' ), array( 'recent', 'popular' ), true ) ? $new_instance['order'] : 'recent',
			'post_type' => in_array( ( $new_instance['post_type'] ?? 'any' ), array( 'any', 'software', 'post' ), true ) ? $new_instance['post_type'] : 'any',
		);
	}
}

/**
 * Category / taxonomy list widget.
 */
class ZabiSoft_Terms_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'zabisoft_terms',
			__( 'ZabiSoft: category list', 'zabisoft' ),
			array(
				'description' => __( 'Lists software categories or blog categories with post counts.', 'zabisoft' ),
				'classname'   => 'zs-widget-terms-widget',
			)
		);
	}

	/**
	 * Front end output.
	 *
	 * @param array $args     Sidebar args.
	 * @param array $instance Widget settings.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$title    = isset( $instance['title'] ) ? $instance['title'] : __( 'Categories', 'zabisoft' );
		$taxonomy = ! empty( $instance['taxonomy'] ) ? $instance['taxonomy'] : 'software_category';
		$count    = isset( $instance['show_count'] ) ? (bool) $instance['show_count'] : true;

		if ( ! taxonomy_exists( $taxonomy ) ) {
			$taxonomy = 'category';
		}

		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => true,
				'number'     => 20,
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return;
		}

		echo isset( $args['before_widget'] ) ? $args['before_widget'] : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( $title ) {
			echo ( isset( $args['before_title'] ) ? $args['before_title'] : '' ) . esc_html( $title ) . ( isset( $args['after_title'] ) ? $args['after_title'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '<ul class="zs-widget-terms">';

		foreach ( $terms as $term ) {
			echo '<li>';
			echo '<a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name );
			if ( $count ) {
				echo ' <span class="zs-count">(' . esc_html( number_format_i18n( $term->count ) ) . ')</span>';
			}
			echo '</a>';
			echo '</li>';
		}

		echo '</ul>';
		echo isset( $args['after_widget'] ) ? $args['after_widget'] : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Admin form.
	 *
	 * @param array $instance Widget settings.
	 * @return void
	 */
	public function form( $instance ) {
		$title      = isset( $instance['title'] ) ? $instance['title'] : __( 'Categories', 'zabisoft' );
		$taxonomy   = isset( $instance['taxonomy'] ) ? $instance['taxonomy'] : ( taxonomy_exists( 'software_category' ) ? 'software_category' : 'category' );
		$show_count = isset( $instance['show_count'] ) ? (bool) $instance['show_count'] : true;
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'zabisoft' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'taxonomy' ) ); ?>"><?php esc_html_e( 'Taxonomy:', 'zabisoft' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'taxonomy' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'taxonomy' ) ); ?>">
				<option value="software_category" <?php selected( $taxonomy, 'software_category' ); ?>><?php esc_html_e( 'Software categories', 'zabisoft' ); ?></option>
				<option value="category" <?php selected( $taxonomy, 'category' ); ?>><?php esc_html_e( 'Blog categories', 'zabisoft' ); ?></option>
				<option value="software_platform" <?php selected( $taxonomy, 'software_platform' ); ?>><?php esc_html_e( 'Platforms', 'zabisoft' ); ?></option>
			</select>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_count ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_count' ) ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_count' ) ); ?>"><?php esc_html_e( 'Show post counts', 'zabisoft' ); ?></label>
		</p>
		<?php
	}

	/**
	 * Save settings.
	 *
	 * @param array $new_instance New values.
	 * @param array $old_instance Old values.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		return array(
			'title'      => sanitize_text_field( $new_instance['title'] ?? '' ),
			'taxonomy'   => sanitize_key( $new_instance['taxonomy'] ?? 'category' ),
			'show_count' => ! empty( $new_instance['show_count'] ),
		);
	}
}

/**
 * Search widget with the large “find your software” style headline.
 */
class ZabiSoft_Search_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'zabisoft_search',
			__( 'ZabiSoft: software search', 'zabisoft' ),
			array(
				'description' => __( 'Highlighted search box, ideal for the top of the sidebar.', 'zabisoft' ),
				'classname'   => 'zs-widget--search',
			)
		);
	}

	/**
	 * Front end output.
	 *
	 * @param array $args     Sidebar args.
	 * @param array $instance Widget settings.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : __( 'Search software', 'zabisoft' );
		$text  = isset( $instance['text'] ) ? $instance['text'] : '';

		echo isset( $args['before_widget'] ) ? $args['before_widget'] : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( $title ) {
			echo ( isset( $args['before_title'] ) ? $args['before_title'] : '' ) . esc_html( $title ) . ( isset( $args['after_title'] ) ? $args['after_title'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '<div class="zs-widget__body">';

		if ( $text ) {
			echo '<p class="zs-widget-search__text">' . esc_html( $text ) . '</p>';
		}

		get_search_form(
			array(
				'zs_placeholder' => __( 'Search software, tools, tutorials…', 'zabisoft' ),
			)
		);

		echo '</div>';
		echo isset( $args['after_widget'] ) ? $args['after_widget'] : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Admin form.
	 *
	 * @param array $instance Widget settings.
	 * @return void
	 */
	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : __( 'Search software', 'zabisoft' );
		$text  = isset( $instance['text'] ) ? $instance['text'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'zabisoft' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php esc_html_e( 'Intro text:', 'zabisoft' ); ?></label>
			<textarea class="widefat" rows="3" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>"><?php echo esc_textarea( $text ); ?></textarea>
		</p>
		<?php
	}

	/**
	 * Save settings.
	 *
	 * @param array $new_instance New values.
	 * @param array $old_instance Old values.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		return array(
			'title' => sanitize_text_field( $new_instance['title'] ?? '' ),
			'text'  => sanitize_textarea_field( $new_instance['text'] ?? '' ),
		);
	}
}

/**
 * Register the widgets.
 *
 * @return void
 */
function zabisoft_register_widgets() {
	register_widget( 'ZabiSoft_Posts_Widget' );
	register_widget( 'ZabiSoft_Terms_Widget' );
	register_widget( 'ZabiSoft_Search_Widget' );
}
add_action( 'widgets_init', 'zabisoft_register_widgets' );
