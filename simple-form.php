<?php
/*
Plugin Name: Simple Form
Description: Simple WordPress contact form with HTML5 validation and a honeypot field
Version: 1.1
Author: Mark Banfill
*/

function html_form_code() {
	echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';
	echo '<label>';
	echo 'Your Name';
	echo '<input type="text" name="cf-name" pattern="[a-zA-Z0-9 ]+" value="' . ( isset( $_POST["cf-name"] ) ? esc_attr( $_POST["cf-name"] ) : '' ) . '" size="40" />';
	echo '</label>';
	echo '<label>';
	echo 'Email';
	echo '<input type="email" name="hp-email" value="' . ( isset( $_POST["hp-email"] ) ? esc_attr( $_POST["hp-email"] ) : '' ) . '" size="40" />';
	echo '</label>';
	echo '<label>';
	echo 'Your Email';
	echo '<input type="email" name="cf-email" value="' . ( isset( $_POST["cf-email"] ) ? esc_attr( $_POST["cf-email"] ) : '' ) . '" size="40" />';
	echo '</label>';
	echo '<label>';
	echo 'Your Message';
	echo '<textarea rows="10" cols="35" name="cf-message">' . ( isset( $_POST["cf-message"] ) ? esc_attr( $_POST["cf-message"] ) : '' ) . '</textarea>';
	echo '</label>';
	echo '<p><input type="submit" name="cf-submitted" value="Send"></p>';
	echo '</form>';
}

function deliver_mail() {

	// if the submit button is clicked, send the email
	if ( isset( $_POST['cf-submitted'] ) ) {

		// check honeypot field for spam
		$honeypot   = sanitize_email( $_POST["hp-email"] );
		if ($honeypot != '') {
			// sanitize form values
			$name    = sanitize_text_field( $_POST["cf-name"] );
			$email   = sanitize_email( $_POST["cf-email"] );
			$subject = "Message from " . $_SERVER['SERVER_NAME'];
			$message = esc_textarea( $_POST["cf-message"] );

			// get the blog administrator's email address
			$to = get_option( 'admin_email' );

			$headers = "From: $name <$email>" . "\r\n";

			// If email has been processed for sending, display a success message
			if ( wp_mail( $to, $subject, $message, $headers ) ) {
				$page = get_page_by_title('Thanks');
	            wp_redirect(get_permalink($page->ID));
	            exit;
			} else {
				echo 'An unexpected error occurred';
			}
		}
	}
}

function cf_shortcode() {
	ob_start();
	deliver_mail();
	html_form_code();

	return ob_get_clean();
}

add_shortcode( 'simple_contact_form', 'cf_shortcode' );

?>
