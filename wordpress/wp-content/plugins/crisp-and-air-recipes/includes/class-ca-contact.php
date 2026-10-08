<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_shortcode( 'ca_contact_form', 'ca_recipe_contact_form_shortcode' );
function ca_recipe_contact_form_shortcode() {
	$status = isset( $_GET['ca_contact'] ) ? sanitize_key( wp_unslash( $_GET['ca_contact'] ) ) : '';
	ob_start();
	?>
	<div id="contact-form">
		<?php if ( 'sent' === $status ) : ?><p class="contact-form__notice" role="status"><?php esc_html_e( 'Thanks for getting in touch. Your message has been sent to the site editor.', 'crisp-and-air-recipes' ); ?></p><?php elseif ( 'error' === $status ) : ?><p class="contact-form__notice contact-form__notice--error" role="alert"><?php esc_html_e( 'We could not send that message. Please check the form and try again.', 'crisp-and-air-recipes' ); ?></p><?php endif; ?>
		<form class="contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="ca_recipe_contact_submit" />
			<?php wp_nonce_field( 'ca_recipe_contact_submit', 'ca_contact_nonce' ); ?>
			<div class="contact-form__honeypot" aria-hidden="true"><label for="ca-contact-company">Leave this field empty<input id="ca-contact-company" type="text" name="company" tabindex="-1" autocomplete="off" /></label></div>
			<label for="ca-contact-name"><?php esc_html_e( 'Your name', 'crisp-and-air-recipes' ); ?><input id="ca-contact-name" name="name" type="text" autocomplete="name" maxlength="100" required /></label>
			<label for="ca-contact-email"><?php esc_html_e( 'Email address', 'crisp-and-air-recipes' ); ?><input id="ca-contact-email" name="email" type="email" autocomplete="email" maxlength="190" required /></label>
			<label for="ca-contact-subject"><?php esc_html_e( 'Subject', 'crisp-and-air-recipes' ); ?><input id="ca-contact-subject" name="subject" type="text" maxlength="150" required /></label>
			<label for="ca-contact-message"><?php esc_html_e( 'Message', 'crisp-and-air-recipes' ); ?><textarea id="ca-contact-message" name="message" rows="6" maxlength="10000" required></textarea></label>
			<p class="contact-form__privacy"><?php esc_html_e( 'Your contact details and message are emailed to the site editor to respond. They are not published as a comment or stored in the recipe database. A short-lived, salted hash may be used to limit abuse. Please do not include sensitive personal information.', 'crisp-and-air-recipes' ); ?></p>
			<button class="button button--dark" type="submit"><?php esc_html_e( 'Send message', 'crisp-and-air-recipes' ); ?> <span aria-hidden="true">→</span></button>
		</form>
	</div>
	<?php
	return ob_get_clean();
}

add_action( 'admin_post_nopriv_ca_recipe_contact_submit', 'ca_recipe_contact_form_submit' );
add_action( 'admin_post_ca_recipe_contact_submit', 'ca_recipe_contact_form_submit' );
function ca_recipe_contact_form_submit() {
	$referer = wp_get_referer();
	$target  = $referer ? $referer : home_url( '/contact/' );
	if ( ! isset( $_POST['ca_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ca_contact_nonce'] ) ), 'ca_recipe_contact_submit' ) ) {
		ca_recipe_contact_redirect( $target, 'error' );
	}
	// Quietly discard simple bot submissions caught by the honeypot.
	if ( ! empty( $_POST['company'] ) ) {
		ca_recipe_contact_redirect( $target, 'sent' );
	}
	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	if ( '' === $name || ! is_email( $email ) || '' === $subject || '' === $message || strlen( $message ) > 10000 ) {
		ca_recipe_contact_redirect( $target, 'error' );
	}
	$remote_addr = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	if ( $remote_addr ) {
		$rate_key = 'ca_contact_rate_' . substr( hash( 'sha256', $remote_addr . wp_salt( 'auth' ) ), 0, 32 );
		$count    = absint( get_transient( $rate_key ) );
		if ( $count >= 5 ) {
			ca_recipe_contact_redirect( $target, 'error' );
		}
		set_transient( $rate_key, $count + 1, HOUR_IN_SECONDS );
	}
	$to          = sanitize_email( get_option( 'admin_email' ) );
	$site_name   = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$mail_subject = sprintf( '[%s] %s', $site_name, $subject );
	$mail_body   = sprintf( "Name: %s\nEmail: %s\n\n%s", $name, $email, $message );
	$sent        = $to && is_email( $to ) && wp_mail( $to, $mail_subject, $mail_body, array( 'Reply-To: ' . $email ) );
	ca_recipe_contact_redirect( $target, $sent ? 'sent' : 'error' );
}

function ca_recipe_contact_redirect( $target, $status ) {
	$target = remove_query_arg( 'ca_contact', $target );
	$target = add_query_arg( 'ca_contact', sanitize_key( $status ), $target );
	wp_safe_redirect( $target . '#contact-form' );
	exit;
}
