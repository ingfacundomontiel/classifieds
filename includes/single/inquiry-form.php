<?php
/**
 * The template for displaying the inquiry form.
 *
 * @package classifieds
 */

// Retrieve the seller's email address from the post meta.
$seller_email = get_post_meta( get_the_ID(), '_classified_email', true );

// Process the form if it has been submitted.
if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['submit_inquiry'] ) ) {

	// Verify the nonce to ensure form submission security.
	if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'send_inquiry' ) ) {

		// Initialize an array to store validation form_errors.
		$form_errors = array();

		// Sanitize form inputs.
		$name  = isset( $_POST['inquiry_name'] ) ? sanitize_text_field( wp_unslash( $_POST['inquiry_name'] ) ) : '';
		$email = isset( $_POST['inquiry_email'] ) ? sanitize_email( wp_unslash( $_POST['inquiry_email'] ) ) : '';
		$phone = isset( $_POST['inquiry_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['inquiry_phone'] ) ) : '';

		// Validate required fields.
		if ( empty( $name ) ) {
			$form_errors[] = 'Name is required.';
		}

		if ( empty( $email ) || ! is_email( $email ) ) {
			$form_errors[] = 'A valid email address is required.';
		}

		if ( empty( $phone ) ) {
			$form_errors[] = 'Phone number is required.';
		}

		// If no form_errors, send the email.
		if ( empty( $form_errors ) ) {
			// Build the email subject and message.
			$subject  = 'Inquiry about your Classified: ' . get_the_title();
			$message  = "You have received a new inquiry regarding your Classified listing.\n\n";
			$message .= "Name: $name\n";
			$message .= "Email: $email\n";
			$message .= "Phone: $phone\n";
			$message .= "\n\nPlease respond to this inquiry at your earliest convenience.";

			// Send the email to the seller.
			$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
			wp_mail( $seller_email, $subject, $message, $headers );

			// Display success message.
			echo '<p style="color: green;">Your inquiry has been successfully sent!</p>';
		} else {
			// Display validation form_errors.
			foreach ( $form_errors as $f_error ) {
				echo '<p style="color: red;">' . esc_html( $f_error ) . '</p>';
			}
		}
	} else {
		echo '<p>Security error. Please try again.</p>';
	}
}
?>

<h3>Contacta al Propietario</h3>
<form method="post" action="">
	<?php wp_nonce_field( 'send_inquiry' ); ?>

	<label for="inquiry_name">Nombre:</label><br>
	<input type="text" id="inquiry_name" name="inquiry_name" required><br><br>

	<label for="inquiry_email">Correo:</label><br>
	<input type="email" id="inquiry_email" name="inquiry_email" required><br><br>

	<label for="inquiry_phone">Teléfono:</label><br>
	<input type="text" id="inquiry_phone" name="inquiry_phone" required><br><br>

	<input type="submit" name="submit_inquiry" value="Enviar">
</form>
