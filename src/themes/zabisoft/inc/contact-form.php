<?php
/**
 * Secure, dependency free contact form ([zabisoft_contact_form]).
 *
 * @package ZabiSoft
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle the contact form submission before the page renders.
 *
 * @return void
 */
function zabisoft_handle_contact_form() {
	if ( ! isset( $_POST['zs_contact_nonce'] ) ) {
		return;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['zs_contact_nonce'] ) );

	if ( ! wp_verify_nonce( $nonce, 'zs_contact_form' ) ) {
		zabisoft_contact_set_status( 'error', __( 'The form session expired. Please try again.', 'zabisoft' ) );

		return;
	}

	// Simple honeypot: bots love filling hidden fields.
	if ( ! empty( $_POST['zs_website_url'] ) ) {
		zabisoft_contact_set_status( 'success', __( 'Thank you, your message has been sent.', 'zabisoft' ) );

		return;
	}

	$name    = isset( $_POST['zs_name'] ) ? sanitize_text_field( wp_unslash( $_POST['zs_name'] ) ) : '';
	$email   = isset( $_POST['zs_email'] ) ? sanitize_email( wp_unslash( $_POST['zs_email'] ) ) : '';
	$subject = isset( $_POST['zs_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['zs_subject'] ) ) : '';
	$message = isset( $_POST['zs_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['zs_message'] ) ) : '';

	if ( '' === $name || '' === $message || ! is_email( $email ) ) {
		zabisoft_contact_set_status( 'error', __( 'Please fill in your name, a valid email address and a message.', 'zabisoft' ) );

		return;
	}

	// Very light rate limiting per IP hash, stored for 10 minutes.
	$ip_hash = 'zabisoft_contact_' . md5( (string) ( $_SERVER['REMOTE_ADDR'] ?? 'unknown' ) );

	if ( get_transient( $ip_hash ) ) {
		zabisoft_contact_set_status( 'error', __( 'You just sent a message. Please wait a few minutes before sending another one.', 'zabisoft' ) );

		return;
	}

	$recipient = apply_filters( 'zabisoft_contact_recipient', get_option( 'admin_email' ) );

	$body = sprintf(
		"%s: %s\n%s: %s\n%s: %s\n\n%s\n",
		__( 'Name', 'zabisoft' ),
		$name,
		__( 'Email', 'zabisoft' ),
		$email,
		__( 'Subject', 'zabisoft' ),
		$subject,
		$message
	);

	$sent = wp_mail(
		$recipient,
		sprintf(
			/* translators: 1: site name, 2: subject. */
			__( '[%1$s] Contact form: %2$s', 'zabisoft' ),
			get_bloginfo( 'name' ),
			$subject ? $subject : __( 'New message', 'zabisoft' )
		),
		$body,
		array(
			'Reply-To: ' . $name . ' <' . $email . '>',
		)
	);

	set_transient( $ip_hash, 1, 10 * MINUTE_IN_SECONDS );

	if ( $sent ) {
		zabisoft_contact_set_status( 'success', __( 'Thank you! Your message has been sent and we will get back to you soon.', 'zabisoft' ) );
	} else {
		zabisoft_contact_set_status( 'error', __( 'The message could not be sent because the site mailer failed. Please write to us by email instead.', 'zabisoft' ) );
	}
}
add_action( 'template_redirect', 'zabisoft_handle_contact_form' );

/**
 * Store a one time status message.
 *
 * @param string $type    success|error.
 * @param string $message Message text.
 * @return void
 */
function zabisoft_contact_set_status( $type, $message ) {
	set_transient(
		'zabisoft_contact_status_' . zabisoft_visitor_key(),
		array(
			'type'    => $type,
			'message' => $message,
		),
		5 * MINUTE_IN_SECONDS
	);
}

/**
 * Anonymous visitor key (no personal data stored).
 *
 * @return string
 */
function zabisoft_visitor_key() {
	$ip = (string) ( $_SERVER['REMOTE_ADDR'] ?? 'unknown' );

	return md5( $ip . wp_salt( 'nonce' ) );
}

/**
 * Print any pending status message.
 *
 * @return void
 */
function zabisoft_contact_render_status() {
	$key    = 'zabisoft_contact_status_' . zabisoft_visitor_key();
	$status = get_transient( $key );

	if ( ! $status || ! is_array( $status ) ) {
		return;
	}

	delete_transient( $key );

	$class = ( 'error' === ( $status['type'] ?? '' ) ) ? 'zs-notice zs-notice--error' : 'zs-notice';

	echo '<div class="' . esc_attr( $class ) . '" role="status">' . esc_html( $status['message'] ?? '' ) . '</div>';
}

/**
 * Shortcode output: the contact form.
 *
 * @return string
 */
function zabisoft_contact_form_shortcode() {
	ob_start();
	zabisoft_contact_render_status();
	?>
	<form class="zs-contact-form" method="post" action="">
		<?php wp_nonce_field( 'zs_contact_form', 'zs_contact_nonce' ); ?>

		<div class="zs-form-row">
			<div class="zs-form-field">
				<label for="zs_name"><?php esc_html_e( 'Your name', 'zabisoft' ); ?> <span class="required">*</span></label>
				<input type="text" id="zs_name" name="zs_name" required autocomplete="name">
			</div>
			<div class="zs-form-field">
				<label for="zs_email"><?php esc_html_e( 'Your email', 'zabisoft' ); ?> <span class="required">*</span></label>
				<input type="email" id="zs_email" name="zs_email" required autocomplete="email">
			</div>
		</div>

		<div class="zs-form-field">
			<label for="zs_subject"><?php esc_html_e( 'Subject', 'zabisoft' ); ?></label>
			<input type="text" id="zs_subject" name="zs_subject">
		</div>

		<div class="zs-form-field">
			<label for="zs_message"><?php esc_html_e( 'Message', 'zabisoft' ); ?> <span class="required">*</span></label>
			<textarea id="zs_message" name="zs_message" rows="7" required></textarea>
		</div>

		<p class="zs-form-note"><?php esc_html_e( 'We only use your details to answer your message. See the privacy policy for more information.', 'zabisoft' ); ?></p>

		<p class="zs-form-footer">
			<label class="zs-hp" aria-hidden="true">
				<?php esc_html_e( 'Leave this field empty', 'zabisoft' ); ?>
				<input type="text" name="zs_website_url" tabindex="-1" autocomplete="off">
			</label>
			<button type="submit" class="zs-btn zs-btn--download"><?php esc_html_e( 'Send message', 'zabisoft' ); ?></button>
		</p>
	</form>
	<?php
	return ob_get_clean();
}
add_shortcode( 'zabisoft_contact_form', 'zabisoft_contact_form_shortcode' );
