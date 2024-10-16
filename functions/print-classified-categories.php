<?php
/**
 * Get and display terms of a taxonomy for a specific post.
 *
 * @package classifieds
 */

/**
 * Get and display terms of a taxonomy for a specific post.
 *
 * @param int    $post_id The ID of the post.
 * @param string $taxonomy The taxonomy slug (e.g., 'category', 'tag', 'classified_category').
 * @param string $error_message Optional. Custom message to display if no terms are found. Default is ''.
 */
function display_post_terms( $post_id, $taxonomy, $error_message = '' ) {
	// Get the terms of the specified taxonomy for the given post.
	$terms = get_the_terms( $post_id, $taxonomy );

	// Check if there's an error or no terms.
	if ( is_wp_error( $terms ) ) {
		echo esc_html( 'Error fetching terms.' );
	} elseif ( ! empty( $terms ) && is_array( $terms ) ) {
		// Create an array to store term names.
		$term_names = array();

		// Loop through each term and add its name to the array.
		foreach ( $terms as $term ) {
			$term_names[] = esc_html( $term->name );
		}

		// Print the list of term names, separated by commas.
		echo esc_html( implode( ', ', $term_names ) );
	} else {
		// Display the custom error message or the default one if no terms are found.
		echo esc_html( $error_message );
	}
}
