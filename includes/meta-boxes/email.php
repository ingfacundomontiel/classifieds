<?php
/**
 * Plugin Name: Classifieds Plugin
 * Description: A plugin to add classifieds functionality to your WordPress site.
 * Version: 1.0
 * Author: Facundo Montiel
 *
 * @package classifieds
 */

/**
 * Adds a meta box to the Classifieds post type for capturing an email address.
 *
 * @return void
 */
function add_email_meta_box() {
	add_meta_box(
		'classified_email_box',        // Unique ID.
		'Correo electrónico',          // Meta box title.
		'classified_email_box_html',   // Callback function to display the meta box HTML.
		'classified',                  // Post type to add the meta box to.
		'normal',                      // Context (location in the post editor).
		'high'                         // Priority of the meta box.
	);
}
add_action( 'add_meta_boxes', 'add_email_meta_box' );

/**
 * Displays the HTML form for the email address meta box in the Classifieds post type.
 *
 * @param WP_Post $post The current post object.
 * @return void
 */
function classified_email_box_html( $post ) {
	// Retrieve the email address from the post meta, if it exists.
	$email = get_post_meta( $post->ID, '_classified_email', true );

	// If the email is not set, default to an empty string.
	if ( empty( $email ) ) {
		$email = ''; // Default value if no email is set.
	}
	?>
	<label for="classified_email">Correo electrónico:</label>
	<input type="email" name="classified_email" value="<?php echo esc_attr( $email ); ?>" size="25" />
	<?php
}

/**
 * Saves the email address meta box data when the post is saved.
 *
 * @param int $post_id The ID of the post being saved.
 * @return void
 */
function save_classified_email_meta( $post_id ) {
	// Check if the email field exists in the POST data and validate it.
	if ( array_key_exists( 'classified_email', $_POST ) ) {
		$email = sanitize_email( $_POST['classified_email'] );

		// If the email is invalid, do not save it.
		if ( ! is_email( $email ) ) {
			add_post_meta( $post_id, '_classified_email', '', true );
		} else {
			update_post_meta( $post_id, '_classified_email', $email );
		}
	}
}
add_action( 'save_post', 'save_classified_email_meta' );
